<template>
    <select ref="select" :name="name" @change="input()" :title="title" :id="id">
        <option v-if="loading" value="" selected data-content="<i class='glyphicon glyphicon-refresh glyphicon-spin'></i> Loading...">...</option>
        <option v-else v-for="option in options" :value="option.id">{{ option.ime }}</option>
    </select>
</template>

<style>
    .glyphicon-spin {
        -webkit-animation: spin 1000ms infinite linear;
        animation: spin 1000ms infinite linear;
    }
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(359deg);
            transform: rotate(359deg);
        }
    }
    @keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(359deg);
            transform: rotate(359deg);
        }
    }
</style>

<script>
    export default{
        data(){
            return {renderedOptions:null, prevLoading:false}
        },

        props: ['name','value','options','title','loading','id','multiple'],

        mounted(){
            if(this.loading) this.$refs.select.setAttribute('disabled', 'disabled');

            if(this.multiple) this.$refs.select.setAttribute('multiple', 'multiple');

            this.init();

            this.prevLoading = this.loading;
            this.renderedOptions = this.options;
        },

        updated(){
            if((this.prevLoading != this.loading) || (this.renderedOptions != this.options)){
                let options = this.$refs.select.options;
                if(!this.loading && options.length) {
                    $(options[0]).removeData('content');  //poslije loading mora da se makne loading znak sa prvog option

                    this.setSelectedOptions();  //poslije loading moraju ponovo da se selektuju odgovarajuce opcije
                }
                this.refresh();

                this.prevLoading = this.loading;
                this.renderedOptions = this.options;
            }
        },

        watch:{
            loading(){
                if(this.loading) this.$refs.select.setAttribute('disabled', 'disabled');
                else this.$refs.select.removeAttribute('disabled');
                //refresh() ce se pozvati u updated()
            },

            value(){
                this.setSelectedOptions();

                this.refresh();
            }
        },

        methods:{
            init(){
                let el = $(this.$refs.select);

                if(el.data('selectpicker')) el.data('selectpicker').destroy();

                el.selectpicker({
                });

                this.renderedOptions = this.options;
            },

            refresh(){
                let el = $(this.$refs.select);
                if(el.data('selectpicker')) el.data('selectpicker').refresh();
            },

            setSelectedOptions(){
                let options = this.$refs.select.options;
                for (let i=0; i < options.length; i++){
                    if(this.multiple) options[i].selected = (this.value.indexOf(options[i].value) != -1) || (this.value.indexOf(parseInt(options[i].value)) != -1);
                    else options[i].selected = this.value == parseInt(options[i].value);
                }
            },

            input(){
                let value;

                if(this.multiple){
                    value = [];
                    let options = this.$refs.select.options;
                    for (let i=0; i < options.length; i++){
                        if(options[i].selected) value.push(options[i].value);
                    }
                }
                else value = this.$refs.select.value;

                this.$emit('input',value);
            }
        }
    }

</script>