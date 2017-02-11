<template>
    <transition name="modal">
        <div class="modal-dialog-mask">
            <div class="modal-dialog-wrapper" @click.self="close()" @keyup.esc="close()">
                <div class="modal-dialog-container">
                    <div v-show="submitting || grupaDuplicating" class="loader-overlay"><div class="loader-width-limiter"><div class="loader-container"><div class="loader"></div></div></div></div>
                    <h2>Nova grupa</h2>
                    <div>
                        <label for="duplikat">Dupliraj grupu:</label>
                        <loading-select id="duplikat" v-model="selectedDuplikat" :options="grupe" title="-- Odaberi grupu --"></loading-select>
                        <!--<button class="btn btn-default" @click.prevent="dupliraj">Dupliraj</button>-->
                    </div>
                    <div :class="'form-group' + (errors.ime?' has-error':'')">
                        <label class="control-label" for="ime">Ime:</label>
                        <input ref="grupaDialogInput" type="text" class="form-control" id="ime" name="ime" v-model="grupa.ime">
                        <span class="help-block" v-if="errors.ime">{{ errors.ime[0] }}</span>
                    </div>

                    <h3>Intervali:</h3>
                    <div class="interval" v-for="(interval,index) in grupa.intervali" v-cloak>
                        <input type="hidden" :name="'intervali['+index+'][dani]'" v-model="interval.dani">

                        <label class="checkbox-inline" v-for="dan in $options.naziviDana">
                            <input type="checkbox" :value="dan" v-model="dani[index]" @change="updateDani(index,$event.target.value,$event.target.checked)">
                            {{ dan }}
                        </label>
                        <button class="btn btn-default" @click.prevent="removeInterval(index)"><span class="glyphicon glyphicon-trash"></span></button>

                        <input type="hidden" :name="'intervali['+index+'][pocetak]'" v-model="interval.pocetak">
                        <input type="hidden" :name="'intervali['+index+'][kraj]'" v-model="interval.kraj">
                        <div v-if="interval.dani==0">
                            Od: <datetime-picker v-model="interval.pocetak" istime="true" isdate="true"></datetime-picker>
                            Do: <datetime-picker v-model="interval.kraj" istime="true" isdate="true"></datetime-picker>
                        </div>
                        <div v-if="interval.dani!=0">
                            Od: <datetime-picker :value="splitDateTime[index].pocetakDate" isdate="true" @input="updateSplitDateTime($options.pocetakDateId, index, arguments[0])"></datetime-picker>
                            Do: <datetime-picker :value="splitDateTime[index].krajDate" isdate="true" @input="updateSplitDateTime($options.krajDateId, index, arguments[0])"></datetime-picker>
                            U intervalu<br/>
                            Od: <datetime-picker :value="splitDateTime[index].pocetakTime" istime="true" @input="updateSplitDateTime($options.pocetakTimeId, index, arguments[0])"></datetime-picker>
                            Do: <datetime-picker :value="splitDateTime[index].krajTime" istime="true" @input="updateSplitDateTime($options.krajTimeId, index, arguments[0])"></datetime-picker>
                        </div>
                        Zone:<br/>
                        <loading-select :name="'intervali['+index+'][zone][]'" title="Odaberi zone..." v-model="interval.zone" :multiple="true" :options="zone" :loading="zoneLoading"></loading-select>
                    </div>
                    <button class="btn btn-default" @click.prevent="pushInterval()"><span class="glyphicon glyphicon-plus"></span></button>
                    <br/>
                    <button class="btn btn-default" @click.prevent="createGrupa()">OK</button>
                </div>
            </div>
        </div>
    </transition>
</template>
<style>
    .modal-dialog-mask {
      position: fixed;
      z-index: 9998;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, .5);
      display: table;
      transition: opacity .3s ease;
    }

    .modal-dialog-wrapper {
      display: table-cell;
      vertical-align: middle;
    }

    .modal-dialog-container {
      position: relative;
      width: 70%;
      max-height: 90%;
      overflow-y: auto;
      margin: 0px auto;
      padding: 20px 30px;
      background-color: #fff;
      border-radius: 2px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
      transition: all .3s ease;
      font-family: Helvetica, Arial, sans-serif;
    }

    .interval {
      border: 1px solid #f3f3f3;
    }

    /*
     * The following styles are auto-applied to elements with
     * transition="modal" when their visibility is toggled
     * by Vue.js.
     *
     * You can easily play with the modal transition by editing
     * these styles.
     */

    .modal-enter {
      opacity: 0;
    }

    .modal-leave-active {
      opacity: 0;
    }

    .modal-enter .modal-dialog-container,
    .modal-leave-active .modal-dialog-container {
      -webkit-transform: scale(1.1);
      transform: scale(1.1);
    }
</style>
<script>
    export default{
        pocetakDateId:0,
        krajDateId:1,
        pocetakTimeId:2,
        krajTimeId:3,
        naziviDana: ['Po','Ut','Sr','Ce','Pe','Su','Ne'],

        data(){
            return{
                grupa: {ime:'', intervali:[]},
                submitting: false,
                errors: {},
                zoneLoading: false,
                zone: [],
                selectedDuplikat:"",
                grupaDuplicating: false
            }
        },

        props:['grupe'],

        computed: {
            /*brojIntervala: function(){
                $('.datetime-picker').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss'
                });
                return this.grupa.intervali.length;
            },*/

            splitDateTime: {
                get: function(){
                    let data = [];
                    this.grupa.intervali.forEach(function(element){
                        let splitElement={};
                        let pocetakSplit = element.pocetak.split(' ');
                        let krajSplit = element.kraj.split(' ');
                        splitElement.pocetakDate = pocetakSplit[0];
                        splitElement.pocetakTime = pocetakSplit[1];
                        splitElement.krajDate = krajSplit[0];
                        splitElement.krajTime = krajSplit[1];
                        data.push(splitElement);
                    });
                    return data;
                },

                set: function(newValue){
                    let length = newValue.length;
                    for (let i = 0; i < length; i++) {
                        this.grupa.intervali[i].pocetak = newValue[i].pocetakDate + ' ' + newValue[i].pocetakTime;
                        this.grupa.intervali[i].kraj = newValue[i].krajDate + ' ' + newValue[i].krajTime;
                    }
                }
            },

            dani: {
                get: function(){
                    let data = [];
                    this.grupa.intervali.forEach(function(element){
                        let dani=[];
                        element = element.dani;
                        for(let i=0; i<7; i++) if(element&(1<<i)) dani.push(this.$options.naziviDana[i]);
                        data.push(dani);
                    }.bind(this));
                    return data;
                },

                set: function(newValue){
                    let length = newValue.length;
                    for (let i = 0; i < length; i++) {
                        let value=0;
                        for(let j=0; j<7; j++) if(newValue[i].indexOf(this.$options.naziviDana[j])!=-1) value|=(1<<j);
                        this.grupa.intervali[i].dani = value;
                    }
                }
            }
        },

        watch: {
            selectedDuplikat: function () {
                if(this.selectedDuplikat != '') this.dupliraj();
            }
            /*brojIntervala: function(){
            }
            dummy: function(){
                $('.datetime-picker').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss'
                });
            }
            'grupa.intervali': function(){
                $('.datetime-picker').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss'
                });
            }*/
        },

        mounted: function(){
            this.$refs.grupaDialogInput.focus();
            this.fetchZone();
            this.pushInterval();

            $('.modal-dialog-container').bind('mousewheel DOMMouseScroll', function(e) {
                var scrollTo = 0;
                e.preventDefault();
                if (e.type == 'mousewheel') {
                    scrollTo = (e.originalEvent.wheelDelta * -1);
                }
                else if (e.type == 'DOMMouseScroll') {
                    scrollTo = 40 * e.originalEvent.detail;
                }
                $(this).scrollTop(scrollTo + $(this).scrollTop());

            });

            /*$('.modal-dialog-container').on( "DOMNodeInserted", function( e ) {
                //console.log( e.target );  // the new element
                var els = $('.datetime-picker');
                els.datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss'
                });
                els.on('dp.change',this.bla);
            }.bind(this));*/
        },

        methods: {
            close: function(){
                this.$emit('close');
            },

            fetchZone: function(){
                this.zoneLoading = true;

                this.$http.get('zone')
                .then((response) => {
                    this.zone = response.data;
                    this.zoneLoading = false;
                }, (response) => {
                    console.log('error:' + response);
                    this.zoneLoading = false;
                });
            },

            createGrupa: function(){
                this.submitting = true;
                let formArray = $('.modal-dialog-container :input').serializeArray();
                this.$http.post('grupe', formArray, {emulateJSON: true}).then((response) => {
                    this.errors={};
                    this.$emit('ok', response.data.id);
                    this.submitting = false;
                    this.close();
                }, (response) => {
                    this.submitting = false;
                    if(response.status == 422){
                        this.errors = response.data;
                        this.$refs.grupaDialogInput.focus();
                        return;
                    }
                    console.log("error:" + response.data.message);
                });
            },

            pushInterval: function(){
                this.grupa.intervali.push({pocetak:'2017-01-27 00:00:00', kraj:'2017-01-27 23:59:59', dani:0, zone:[]});
            },

            removeInterval: function(index){
                this.grupa.intervali.splice(index,1);
                if(this.grupa.intervali.length==0) this.pushInterval();
            },

            updateSplitDateTime: function(id, index, value){
                let temp = this.splitDateTime;
                switch(id){
                    case this.$options.pocetakDateId: temp[index].pocetakDate = value; break;
                    case this.$options.krajDateId: temp[index].krajDate = value; break;
                    case this.$options.pocetakTimeId: temp[index].pocetakTime = value; break;
                    case this.$options.krajTimeId: temp[index].krajTime = value; break;
                }
                this.splitDateTime = temp;
            },

            updateDani: function(index, value, checked){
                let temp = this.dani;
                let i = temp[index].indexOf(value);
                if(i!=-1 && !checked) temp[index].splice(i,1);
                else if(i==-1 && checked) temp[index].push(value);
                this.dani = temp;
            },

            dupliraj: function(){
                this.grupaDuplicating = true;
                this.$http.get('grupe/'+this.selectedDuplikat)
                    .then((response) => {
                        let temp = response.data;
                        for(let i=0; i<temp.intervali.length; i++)
                        {
                            let zone = [];
                            for(let j=0; j<temp.intervali[i].zone.length; j++)
                            {
                                zone.push(temp.intervali[i].zone[j].id);
                            }
                            temp.intervali[i].zone = zone;
                        }
                        this.grupa = temp;
                        this.grupaDuplicating = false;
                    }, (response) => {
                        console.log('error:' + response);
                        this.grupaDuplicating = false;
                    });
            }
        }
    }
</script>
