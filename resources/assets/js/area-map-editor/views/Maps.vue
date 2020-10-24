<template>
    <div class="maps-wrapper">
        <div><button @click="newMap">New Map</button></div>
hi maps
        <div class="flex-container"  v-for="map in maps"  v-bind:key="map.id">
            <router-link :to="'/maps/'+map.id" >
                    {{ map.name}}
            </router-link>
            <router-link :to="'/maps/'+map.id" >

            {{map.url}}

            </router-link>
            <button @click="clone(map)">Clone</button>

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
                    name: 'new-map',
                    url: '',
                    boxes: []
                }
                axios.post('/api/area-maps', arg).then( response => {
                    this.$router.push('maps/'+response.data.map.id)
                }).catch(errors => {            this.$store.commit('clear');
                    axios.get('/api/area-maps/'+ this.$route.params.id).then(response => {
                        const data = response.data;
                        if(data.boxes && data.boxes.length > 0){
                            data.boxes.forEach(box => {
                                if(!box.neighbors){
                                    box.neighbors = [];
                                }
                                this.$store.commit('createBox', box);
                            })

                        }
                        // this.mapWidth = data.width;
                        this.$store.commit('updateWidth', data.width);
                        this.$store.commit('updateMapName', data.name);
                        this.$store.commit('updateUrl', data.url);
                        this.$store.commit('updateRev', data._rev);
                    }).catch(errors => {
                        console.log(errors);
                    });
                    console.log(errors);
                    // this.errors = errors.response.data.errors;
                });
            },
            clone(map){
                // this.$store.commit('clear');
                axios.get('/api/area-maps/'+ map.id).then(response => {
                    const data = response.data;
                    let newMap = {...data};
                    delete newMap._id;
                    delete newMap._rev;
                    newMap.name = "Clone of "+newMap.name;

                    axios.post('/api/area-maps', newMap).then( response => {
                        this.$router.push('/maps/'+response.data.map.id)
                    }).catch(errors => {
                        alert("Error on write");
                        });
                    // if(data.boxes && data.boxes.length > 0){
                    //     data.boxes.forEach(box => {
                    //         if(!box.neighbors){
                    //             box.neighbors = [];
                    //         }
                    //         this.$store.commit('createBox', box);
                    //     })
                    //
                    // }
                    // this.mapWidth = data.width;
                    // this.$store.commit('updateWidth', data.width);
                    // this.$store.commit('updateMapName', data.name);
                    // this.$store.commit('updateUrl', data.url);
                    // this.$store.commit('updateRev', data._rev);
                }).catch(errors => {
                    console.log(errors);
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
    .flex-container{
        display:flex;
        justify-content: space-between;
        &:hover{
            background-color:aliceblue;
        }
    }
</style>