<template>
    <div class="maps-wrapper">
        <div><button @click="newMap">New Map</button></div>
hi maps
        <div v-for="map in maps"  v-bind:key="map.id">
            <router-link class="flex-container" :to="'/maps/'+map.id" >
                <div>
                    {{ map.name}}
                </div>
                <div>
                    {{map.url}}
                </div>
            </router-link>

        </div>
    </div>
</template>

<script>
    export default {
        name: "Maps",
        data: () =>{
            return { maps: []}
        },
        mounted(){
            axios.get('/api/area-maps/').then(response => {
                this.maps = response.data.maps
            }).catch(errors => {
                console.log(errors);
            });
        },
        methods: {
            newMap(){
                const arg = {
                    name: '',
                    url: '',
                    boxes: []
                }
                axios.post('/api/area-maps', arg).then( response => {
                    this.$router.push('maps/'+response.data.map.id)
                }).catch(errors => {
                    console.log(errors);
                    // this.errors = errors.response.data.errors;
                });
            }
        }
    }
</script>

<style lang="scss" scoped>

    .maps-wrapper{
        border: 10px solid lightgray;
        border-radius: 10px;
        padding: 10px;
    }
    a.flex-container{
        display:flex;
        justify-content: space-between;
        &:hover{
            background-color:aliceblue;
        }
    }
</style>