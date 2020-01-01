<template>
    <div>
        <img class="the-image" src="../assets/BPMap10.2.jpg" alt="">

        test test test
        {{ sta }}
    </div>
</template>

<script>
    export default {
        name: "test-me",
        mounted(){
            debugger;
        },
        computed:{
           sta(){
               debugger;
               return this.$store.state;
           }
        }
    }
</script>

<style scoped>

</style>