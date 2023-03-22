<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Exports\ArticleHistorique;
use App\Models\Commande;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ArticleController extends Controller
{
    public function index()
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $articles = DB::table('articles')
                    ->where(function ($query) use ($user) {
                        $query->where('articles.id_client', $user->id)
                            ->orwhere('articles.id_client', $user->superviseur);
                    })
                    ->select('articles.id', 'articles.commentaire', 'articles.nom_article', 'articles.prix_article', 'articles.stock_article', 'articles.etat_article')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(20);
                return response()->json([
                    'data' => $articles
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function afficheArticleDisponible()
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $articles = DB::table('articles')
                    ->where(function ($query) use ($user) {
                        $query->where('articles.id_client', $user->id)
                            ->orwhere('articles.id_client', $user->superviseur);
                    })
                    ->where('articles.etat_article', 'En stock')
                    ->where('articles.stock_article', '>', 0)
                    ->select('articles.id', 'articles.commentaire', 'articles.nom_article', 'articles.prix_article', 'articles.stock_article', 'articles.etat_article')
                    ->orderBy('updated_at', 'desc')
                    ->get();
                for ($i = 0; $i < count($articles); $i++) {
                    $article = Commande::whereIn('commandes.etat_commande', ['CONFIRMED', 'PROCESSING', 'PICKUP', 'INHOUSE'])
                        ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                        ->where('commandes.id_client', $user->id)
                        ->where('detailscommandes.id_article', $articles[$i]->id)
                        ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                        ->first();
                    // that if means it's the first confirmed of the article
                    if ($article->qnt_article == null) {
                        $article_in_stock = Article::where('articles.id', $articles[$i]->id)
                            ->selectRaw('articles.stock_article as qnt_article_stock')
                            ->first();
                        if ($article_in_stock->qnt_article_stock <= 0) {
                        } else {
                            $articles[$i]->qnt = $article_in_stock->qnt_article_stock;
                        }
                    } else {
                        //pour savoir la quantite exist en stock
                        $article_in_stock = Article::where('articles.id', $articles[$i]->id)
                            ->selectRaw('articles.stock_article as qnt_article_stock')
                            ->first();
                        if ($article_in_stock->qnt_article_stock - $article->qnt_article <= 0) {
                        } else {
                            $articles[$i]->qnt = $article_in_stock->qnt_article_stock - $article->qnt_article;
                        }
                    }
                }

                return response()->json([
                    'data' => $articles,

                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            "nom_article" => 'required|string|max:200',
            "commentaire" => 'nullable|string|max:255',
            "prix_article" => "required",
        ]);

        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {

                $statut = Article::create([
                    "nom_article" => $request->nom_article,
                    "commentaire" => $request->commentaire,
                    "prix_article" => $request->prix_article,
                    "stock_article" => 0,
                    "etat_article" => "En traitement",
                    "id_client" => $user->id,
                ]);
                if ($statut) {
                    return response()->json([
                        'message' => 'Article created successfully'
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Erreur'
                    ]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e
            ]);
        }
    }
    public function show($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $commande = Commande::find($id);
                return response()->json([
                    'data' => $commande
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $Commande = Commande::find($id);
                $Commande->delete();
                return response()->json("Record deleted!");
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function checkStock(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            //pour savoir le nombre des articles deja confirmer
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $data = [];
                for ($i = 0; $i < count($request->all()); $i++) {
                    //pour savoir le nombre des articles deja confirmer
                    $article = Commande::where('commandes.etat_commande', 'CONFIRMED')
                        ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                        ->where('commandes.id_client', $user->id)
                        ->where('detailscommandes.id_article', $request[$i]['id'])
                        ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                        ->first();
                    // that if means it's the first confirmed of the article
                    if ($article->qnt_article == null) {
                        $article_in_stock = Article::where('articles.id', $request[$i]['id'])
                            ->selectRaw('articles.stock_article as qnt_article_stock')
                            ->first();
                        $articles = $article_in_stock->qnt_article_stock;
                    } else {
                        //pour savoir la quantite exist en stock
                        $article_in_stock = Article::where('articles.id', $request[$i]['id'])
                            ->selectRaw('articles.stock_article as qnt_article_stock')
                            ->first();
                        $articles = $article_in_stock->qnt_article_stock - $article->qnt_article;
                    }
                    if ($request[$i]['quantite'] <= 0) {
                        return response()->json([
                            'message' => 'Erreur la quantité doit etre superieur de 0'
                        ]);
                    } elseif ($request[$i]['quantite'] > (int)$articles) {
                        return response()->json([
                            'message' => 'Erreur la quantité entrer plus que le stock'
                        ]);
                    }
                    $data[$request[$i]['id']] = $articles;
                }
                return $data;
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function downloadSticker(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $articles = DB::table('articles')
                    ->join('clients', 'articles.id_client', '=', 'clients.id')
                    ->join('villes', 'clients.id_ville', '=', 'villes.id')
                    ->where('articles.id_client', $user->id)
                    ->where('articles.id', $request->id)
                    ->select(
                        'articles.id',
                        'clients.company',
                        'clients.website',
                        'clients.telephone',
                        'articles.nom_article',
                        'articles.prix_article',
                        'clients.nom',
                        'clients.prenom',
                        'villes.nom_ville'
                    )
                    ->get();

                $data = ['data' => $articles];
                $pdf = PDF::loadView('article', $data)->setOption('margin-top', 1)
                    ->setOption('margin-right', 1)
                    ->setOption('margin-left', 1)
                    ->setOption('margin-bottom', 1);;
                return $pdf->stream('document.pdf');
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function downloadHistoriqueArticle(Request $request)
    {
        return Excel::download(new ArticleHistorique($request->id), 'ArticleHistorique.xlsx');
    }
}
