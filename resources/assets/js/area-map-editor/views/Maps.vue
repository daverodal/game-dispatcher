<template>
    <div class="maps-comp-wrapper">
        <div><button @click="newMap">New Map</button></div>
      <div class="">
        <div class="row"  v-for="map in maps"  v-bind:key="map.id">

          <div class="col-4">
            <router-link :to="'/maps/'+map.id" >
              {{ map.name }}----{{ map.gameName }}.{{map.scenarioName}}
            </router-link>
          </div>
          <div class="col-6">
            <router-link :to="'/maps/'+map.id" >

              {{map.url}}

            </router-link>
          </div>
        <div class="col-1">
          <button @click="clone(map)">Clone</button>

        </div>
          <div class="col-1">
            <button @click="remove(map)">Delete</button>

          </div>


        </div>
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
          this.fetchData();
        },
        methods: {
          fetchData(){
            axios.get('/api/area-maps/').then(response => {

              console.log("Flashed it ");
              this.flash('Data loaded', 'success');

              this.maps = response.data.maps
            }).catch(errors => {
              console.log(errors);
            });
          },
            newMap(){
                const arg = {
                    name: 'new-map',
                    url: '',
                    boxes: [],
                    borderBoxes: [],
                    width: 1000
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

            },
          remove(map){
            // // this.$store.commit('clear');
            axios.delete('/api/area-maps/'+ map.id).then(response => {
                  this.fetchData();
            }).catch(errors => {
              console.log(errors);
            });

          }
        }
    }
</script>

<style lang="scss" scoped>

    .maps-comp-wrapper{
        border: 10px solid lightgray;
        border-radius: 10px;
        padding: 10px;
      text-align: left;
      max-width:1650px;
      position: relative;
      z-index: 3;
    }
    .flex-container{
        display:flex;
        justify-content: space-between;
        &:hover{
            background-color:aliceblue;
        }
    }
</style>