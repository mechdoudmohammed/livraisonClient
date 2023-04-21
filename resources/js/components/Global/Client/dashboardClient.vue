<template>
	<div id="PageContainer">
		<navbar-client-component  :Client="currentUser" v-if="currentUser"></navbar-client-component>
		<sidebar-client-component :Client="currentUser"  v-if="currentUser"></sidebar-client-component>

	</div>
</template>

<script>

export default {
	props: ['Client'],
	name: "dashboardClient",

	data() {
		return {
			errors: {},
			currentUser: false,
			token: localStorage.getItem('token') || "",
		}
	},
	async mounted() {

		if (localStorage.getItem('locale') == 'ar') {
			document.getElementById("PageContainer").setAttribute("dir", "rtl");
		}


		axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
		if (this.token != "") {
			await axios.get('api/client').then((response) => {
				this.currentUser = response.data;
			}).catch((errors) => {
				if (errors.response.status === 401) {
					localStorage.removeItem('token');
				}
			})
		};
		if (this.$route.name === 'dashboardClient') {
			axios.get('api/importantNotification').then((response) => {
				if (response.data.data['nbrNotification'] > 0) {
					Swal.fire({
						icon: 'warning',
						title: 'You have ' + response.data.data['nbrNotification'] + ' important notification',
						// html:
						// 	'<div id="vehicule_info"></div>' +
						// 	'<table class="table table-borderless" style="font-weight:600;text-align:left;">' +
						// 	response.data.data
						// 		.map(notification => `<tr><td>${vehicule.nature_maintenance}:</td><td>${vehicule.nom} ${vehicule.immatriculation}</td><td style='color:red'>${vehicule.duree} J</td></tr>`)
						// 		.join('')
						// 	+ '</table>',
					})
				}

			}).catch((errors) => {

			})
		}


	}

}


</script>
