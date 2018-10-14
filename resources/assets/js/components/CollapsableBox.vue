<template>
    <div>
        <div class="col-xs-2 button-wrapper">
            <button @click="show = !show">show/hide</button>
        </div>
        <slot v-if="show" class="col-xs-12"></slot>

    </div>
</template>

<script>
    export default {
        name: "CollapsableBox",
        data(){
            return {show: false}
        },
        methods:{
            toggle: function(){
                this.show = !this.show;
            }
        }
    }
</script>

<style scoped>

</style>