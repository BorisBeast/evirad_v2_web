<template>
    <div class='input-group date' ref="picker">
        <input type='text' :value="datetime" class="form-control" />
        <span class="input-group-addon">
            <span v-if="hastime && !hasdate" class="glyphicon glyphicon-time"></span>
            <span v-else class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>
</template>
<style>

</style>
<script>
    export default{
        data(){
            return{
                datetime: '1900-01-01 00:00:00',
                format: 'YYYY-MM-DD HH:mm:ss',
                hasdate: true,
                hastime: false,
            }
        },

        props: ['value', 'isdate', 'istime'],

        watch: {
            value(){
                this.datetime = this.value;
            },
        },

        mounted(){
            this.init();
        },

        updated(){
            if((!!this.isdate != this.hasdate) || (!!this.istime != this.hastime)) {
                this.init();
            }
        },

        methods:{
            init(){
                let el = $(this.$refs.picker);

                if(el.data("DateTimePicker")) el.data("DateTimePicker").destroy();

                this.datetime = this.value;

                this.hasdate = !!this.isdate;
                this.hastime = !!this.istime;

                if(this.hasdate && this.hastime) this.format = 'YYYY-MM-DD HH:mm:ss';
                else if(this.hastime) this.format = 'HH:mm:ss';
                else this.format = 'YYYY-MM-DD';

                el.datetimepicker({
                    format: this.format,
                    sideBySide: true
                }).on('dp.change',this.changed);
            },

            changed(event){
                this.datetime = event.date.format(this.format);
                this.$emit('input', this.datetime);
            }
        }
    }

</script>
