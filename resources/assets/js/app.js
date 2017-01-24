/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('sluzba-dialog', require('./components/SluzbaDialog.vue'));

const app = new Vue({
    el: '#app',

    data: {
        radnici: [],
        searchQuery: '',
        showDetails: false,
        radnikDetails: {},
        radnikDetailsTitle: '',
        karticaKod: '',
        errors: {},
        message: {},
        previousDetailsRequest: null,
        listLoading: false,
        detailsLoading: false,
        sluzbe: [],
        sluzbeLoading: false,
        selectedSluzba: '',
        showSluzbaDialog: false
    },

    computed: {
        filteredRadnici: function () {
            return this.radnici.filter(function (radnik) {
                return (radnik.prezime + radnik.ime + radnik.prezime).toLowerCase().indexOf(this.searchQuery.toLowerCase()) !== -1;
            }.bind(this)).sort(function (radnik1, radnik2) {
                let r1Ime = radnik1.prezime + " " + radnik1.ime;
                let r2Ime = radnik2.prezime + " " + radnik2.ime;
                return r1Ime.localeCompare(r2Ime);
            });
        },
    },

    methods: {
        fetchList: function () {
            this.listLoading = true;
            this.$http.get('radnici')
                .then((response) => {
                    this.radnici = response.data;
                    this.listLoading = false;
                }, (response) => {
                    console.log('error:' + response);
                    this.listLoading = false;
                });
        },

        loadDetailsClick: function (id) {
            this.showMessage(null, null); //scroll to top
            this.message = {};
            this.loadDetails(id);
        },

        loadDetails: function (id) {
            this.detailsLoading = true;
            this.$http.get('radnici/'+id, {
                // use before callback
                before(request) {
                    // abort previous request, if exists
                    if (this.previousDetailsRequest) {
                        this.previousDetailsRequest.abort();
                    }

                    // set previous request on Vue instance
                    this.previousDetailsRequest = request;
                }})
                .then((response) => {
                    this.radnikDetails = response.data;
                    this.showDetails = true;
                    this.radnikDetailsTitle = this.radnikDetails.ime + " " + this.radnikDetails.prezime + " [" + this.radnikDetails.id + "]";
                    this.errors={};
                    this.selectedSluzba = this.radnikDetails.sluzba;
                    this.detailsLoading = false;
                }, (response) => {
                    console.log('error:' + response);
                    this.detailsLoading = false;
                });
        },

        sendData: function () {
            let formArray = $('form').serializeArray();
            this.detailsLoading = true;
            if(!this.radnikDetails.id)   //novi radnik
            {
                this.$http.post('radnici', formArray, {emulateJSON: true}).then((response) => {
                    this.loadDetails(response.data.id);
                    this.fetchList();
                    this.showMessage("success", response.data.message);
                }, (response) => {
                    this.detailsLoading = false;
                    if(response.status == 422){
                        this.errors = response.data;
                        this.message = {};
                        return;
                    }
                    this.showMessage("error", response.data.message);
                });
            }
            else  //update radnik
            {
                this.$http.patch('radnici/'+this.radnikDetails.id, formArray, {emulateJSON: true}).then((response) => {
                    this.loadDetails(this.radnikDetails.id);
                    this.fetchList();
                    this.showMessage("success", response.data.message);
                }, (response) => {
                    this.detailsLoading = false;
                    if(response.status == 422){
                        this.errors = response.data;
                        this.message = {};
                        return;
                    }
                    this.showMessage("error", response.data.message);
                });
            }
        },

        showMessage: function (type, text) {
            this.message.type = type;
            this.message.text = text;
            $('html, body').animate({
                scrollTop: '0px'
            }, 'fast');
        },

        noviRadnik: function() {
            if (this.previousDetailsRequest) {
                this.previousDetailsRequest.abort();
                this.previousDetailsRequest=null;
            }
            this.detailsLoading = false;
            this.radnikDetails = {id:0, ime:"", prezime:"", komentar:"", kartice:[]};
            this.showDetails = true;
            this.radnikDetailsTitle = "Novi radnik";
            this.karticaKod = '';
            this.errors = {};
            this.message = {};
            this.selectedSluzba = "";
        },

        karticaKodBlur: function () {
            if(this.karticaKod) {
                let novaKartica = {id:'', kod:this.karticaKod};
                this.radnikDetails.kartice.push(novaKartica);
                this.karticaKod = '';
            }
        },

        sluzbaChanged: function (event) {
            //if(event.target.value==-1) {
            if(this.selectedSluzba==-1) {
                this.showSluzbaDialog = true;
                this.selectedSluzba = "";
            }
        },

        fetchSluzbe: function (select=null) {
            this.sluzbeLoading = true;
            this.$http.get('sluzbe')
                .then((response) => {
                    this.sluzbe = response.data;
                    this.sluzbeLoading = false;
                    if(select) this.selectedSluzba = select;
                }, (response) => {
                    console.log('error:' + response);
                    this.sluzbeLoading = false;
                });
        },

        sluzbaCreated: function(sluzba) {
            this.fetchSluzbe(sluzba); //promijeni listu i selektuj novododatu sluzbu
        }
    },

    created: function () {
        this.fetchList();
        this.noviRadnik();
        this.fetchSluzbe();
    }
});

$('body').popover({
    selector: '[data-toggle="popover"]',
    trigger: 'hover',
    placement: "top",
    html: true,
    container: 'body'
});