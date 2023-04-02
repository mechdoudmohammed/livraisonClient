<template>
    <div class="content-wrapper-customise">

        <div class="page-header-dashboard">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span>{{$t('message.Packages')}}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
           
                        <div class="row">
                            <div class="col-6">
                                <vs-select class="selectExample" v-model="formDataCherche3.selected_option3"
                                    @change="getPackages(formDataCherche3.selected_option3)">
                                    <vs-select-item :key="index" :value="item.value" :text="item.text"
                                        v-for="item, index in options3" />
                                </vs-select>
                            </div>
               


                        </div>

                        <vs-table stripe :data="packagesClient.data">
                            <template slot="thead">
                                <vs-th>
                                    {{$t('message.Package')}}
                                </vs-th>
                                <vs-th v-if="Client.stock == 1">
                                    {{$t('message.Type')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Orders_Count')}}
                                </vs-th>
                                <vs-th>
                                    {{$t('message.Operation')}}
                                </vs-th>

                            </template>

                            <template slot-scope="{data}">
                                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                                    <vs-td :data="tr.id_package">
                                        {{ tr.id_package }}
                                        <p><time-ago :datetime="tr.updated_at" long :locale="locale"></time-ago> </p>
                                    </vs-td>
                                    <vs-td :data="tr.id_article" v-if="Client.stock == 1">
                                        <b v-if="tr.type_commande == 'ramassage'"
                                            class="badge badge badge-gradient-info">R</b>
                                        <b v-if="tr.type_commande == 'stock'"
                                            class="badge badge badge-gradient-primary">S</b>
                                    </vs-td>
                                    <vs-td :data="tr.nombre_commande">
                                        {{ tr.nombre_commande }}
                                    </vs-td>
                                    <vs-td :data="tr.id_package">
                                        <button type="button" class="btn btn-valide"
                                            @click.prevent="getPackagePrint(tr.id_package, false)"><i
                                                class="fa fa-print"></i></button>
                                                
                                                <button type="button" class="btn btn-success"
                                            @click.prevent="getPackagePrint(tr.id_package, false,'smallStickers')"><i
                                                class="mdi mdi-printer"></i></button>




                                        <button type="button" class="btn btn-danger"
                                            @click.prevent="getPackagePrint(tr.id_package, true)"><i
                                                class="fa fa-download"></i></button>
                                        <button type="button" class="btn btn-history"
                                            v-if="tr.type_commande == 'ramassage'"
                                            @click.prevent="getBonRamassagePrint(tr.id_package)"><i
                                                class="fas fa-file-alt"></i></button>



                                                
                                    </vs-td>



                                </vs-tr>
                            </template>
                        </vs-table>
                        <vs-pagination v-if="locale=='ar'" @input="getPackages(0)" :max="9" :total="packagesClient.last_page"
                            v-model="packagesClient.current_page"  prev-icon="arrow_forward"
                            next-icon="arrow_back"></vs-pagination>
                            <vs-pagination v-else @input="getPackages(0)" :max="9" :total="packagesClient.last_page"
                            v-model="packagesClient.current_page" prev-icon="arrow_back"
                            next-icon="arrow_forward"></vs-pagination>
                    </div>
                </div>
            </div>
        </div>



    </div>
</template>
<script>
import axios from 'axios'
import { TimeAgo } from 'vue2-timeago'
import 'vue2-timeago/dist/vue2-timeago.css'
export default {
    props: ['Client'],
    name: 'PackageClientComponent',
    components: {
        TimeAgo
    },
    data() {
        return {
            token: localStorage.getItem('token'),
            locale: localStorage.getItem("locale"),
            nom_err: '',
            test: 1,
            edit: false,
            errors: {},
            packagesClient: {
                'data': '',
                'current_page': 1,
                'last_page': 1
            },
            getpackageClient: {},
            options3: [
            { text: '1-20 '+this.$t('message.Items'), value: '20' },
                { text: '1-50 '+this.$t('message.Items'), value: '50' },
                { text: '1-150 '+this.$t('message.Items'), value: '150' },
                { text: '1-200 '+this.$t('message.Items'), value: '200' },

            ],
            formDataCherche3: {
                selected_option3: '20',

            },
      
        }
    },
    methods: {
  
        async getBonRamassagePrint(id) {
            this.$vs.loading({ color: "#22c16b" });
            let formData = new FormData()
            formData.append('id', id)
            _.each(this.formData, (value, key) => { formData.append(key, value) })

            await axios.post('/api/bonRamassage', formData, { responseType: 'blob' })
                .then(res => {
                    const link = document.createElement('a');
                    // create a blobURI pointing to our Blob
                    link.href = URL.createObjectURL(res.data);
                    link.download = id+'.pdf';
                    // some browser needs the anchor to be in the doc
                    document.body.append(link);
                    link.click();
                    link.remove();
                    // window.open(URL.createObjectURL(res.data),'Download') 
                })
                .catch(error => console.log(res)).finally(() => this.$vs.loading.close());


        },
        async getPackagePrint(id, download,type) {

            let formData = new FormData()
            formData.append('id', id)
            formData.append('download', download)
            formData.append('type', type)
            _.each(this.formData, (value, key) => { formData.append(key, value) })
            if (download) {
                this.$vs.loading({ color: "#22c16b" });
                await axios.post('/api/getPackage', formData, { responseType: 'blob' })
                    .then(res => {
                        const link = document.createElement('a');
                        // create a blobURI pointing to our Blob
                        link.href = URL.createObjectURL(res.data);
                        link.download = id+'.pdf';
                        // some browser needs the anchor to be in the doc
                        document.body.append(link);
                        link.click();
                        link.remove();
                        // window.open(URL.createObjectURL(res.data),'Download') 

                    })
                    .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
            } else {
                this.$vs.loading({ color: "#22c16b" });
                await axios.post('/api/getPackage', formData, { responseType: 'blob' })
                    .then(res => { window.open(URL.createObjectURL(res.data)) })
                    .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
            }

        },
        async getPackages(count_nbr) {
            this.$vs.loading({ color: "#22c16b" });
            setTimeout(async () => {
                if (count_nbr > 1) {
                    await axios.get('/api/showPackage?page=' + this.packagesClient.current_page + '&count_nbr=' + count_nbr)
                        .then(res => { this.packagesClient = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close());
                } else {
                    await axios.get('/api/showPackage?page=' + this.packagesClient.current_page + '&count_nbr=20')
                        .then(res => { this.packagesClient = res.data.data; })
                        .catch(error => console.log(res)).finally(() => this.$vs.loading.close())
                }
            }, 200);


        },



    },
    async beforeMount() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        await this.getPackages();

    },
    mounted() {

        $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');


    },

}




</script>
