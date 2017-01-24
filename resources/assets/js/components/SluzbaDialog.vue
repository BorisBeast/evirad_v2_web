<template>
    <transition name="modal">
        <div class="modal-dialog-mask">
            <div class="modal-dialog-wrapper" @click.self="close()" @keyup.esc="close()">
                <div class="modal-dialog-container">
                    <div v-show="submitting" class="loader-overlay"><div class="loader-width-limiter"><div class="loader-container"><div class="loader"></div></div></div></div>
                    <div :class="'form-group' + (errors.ime?' has-error':'')">
                        <label class="control-label" for="ime">Nova sluzba:</label>
                        <input ref="sluzbaDialogInput" type="text" class="form-control" id="ime" name="ime" v-model="sluzba">
                        <span class="help-block" v-if="errors.ime">{{ errors.ime[0] }}</span>
                    </div>
                    <button class="btn btn-default" @click.prevent="createSluzba()">OK</button>
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
      width: 100%;
      height: 100%;
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
      width: 300px;
      margin: 0px auto;
      padding: 20px 30px;
      background-color: #fff;
      border-radius: 2px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
      transition: all .3s ease;
      font-family: Helvetica, Arial, sans-serif;
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

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
      -webkit-transform: scale(1.1);
      transform: scale(1.1);
    }
</style>
<script>
    export default{
        data(){
            return{
                sluzba:'',
                submitting: false,
                errors: {}
            }
        },

        mounted: function(){
            this.$refs.sluzbaDialogInput.focus();
        },

        methods: {
            close: function(){
                this.$emit('close');
            },

            createSluzba: function(){
                this.submitting = true;

                this.$http.post('sluzbe', {'ime':this.sluzba}, {emulateJSON: true}).then((response) => {
                    this.errors={};
                    this.$emit('ok', response.data.id);
                    this.submitting = false;
                    this.close();
                }, (response) => {
                    this.submitting = false;
                    if(response.status == 422){
                        this.errors = response.data;
                        this.$refs.sluzbaDialogInput.focus();
                        return;
                    }
                    console.log("error:" + response.data.message);
                });
            }
        }
    }
</script>
