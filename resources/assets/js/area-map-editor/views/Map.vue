<template>
    <div class="component-wrapper">
        hi all
        <my-button></my-button>
    yeah
        <a  class="btn btn-danger" href="#" @click="$router.back()">
            < Back
        </a>
        You have {{boxes.length}} boxes. Map Name is "{{ mapName }}"
        <div class="meta-wrapper">
            <div class="flex-container">
                <label for="name">Map Name</label>
                <input type="text" v-model="mapName" class="text-gray-900 pt-8 w-full" id="name"
                       placeholder="map name" @input="updateMapName" >

            </div>
            <div  class="flex-container">
                <label for="url">Map Url</label>
                <input type="text" v-model="mapUrl" class="text-gray-900 pt-8 w-full" id="url"
                       placeholder="url to map" @input="updateUrl" >
            </div>
            <div  class="flex-container">
                <label for="width">Map Width</label>
                <input type="text" v-model="mapWidth" class="text-gray-900 pt-8 w-full" id="width"
                       placeholder="map width" @input="updateWidth" >
            </div>
        </div>

        <div v-if="selected !== null">
            id: {{ selectedId }} <br>
            x: {{ selectedBox.x }}<br>
            y: {{ selectedBox.y }}<br>
            Name: <input type="text" :value="selectedBox.name" @input="updateName"  placeholder="enter name">
            TerrainType:
            <select @input="debugMe" v-model="selectedTerrain">
            <option value=""  disabled>Please Select TerrainType</option>
            <option >water</option>
            <option >forest</option>
            <option >desert</option>
            <option >mountain</option>
            <option >pasture</option>
            <option >field</option>
        </select>
            Name: <input type="text" v-model="selectedTerrain" @input="updateTerrain"  placeholder="enter terrain type">
            Has a City?:<input type="checkbox" :checked="selectedBox.isCity" @input="updateIsCity">
            <p>
                Name is: {{ selectedBox.name }}
            </p>

        </div>
        <div v-if="selectedBox && selectedBox.neighbors">
            <span v-for="neighbor in selectedBox.neighbors">
                {{neighbor}}
            </span>
        </div>
        <button @click="neighborMode = !neighborMode">Neighbor mode</button>
        <button @click="addBox">add box</button>
        <button @click="clear">Clear</button>
        <button @click="saveMap">Save</button>
        <div v-if="neighborMode" class="neighbor-mode-banner">
            Neighbor Mode Enabled,
        </div>
        <div class="map-wrapper">
            <img class="the-image" :style="'width: '+mapWidth+'px'" :src="mapUrl" alt="map">

            <Movable :neighbor-mode="neighborMode" v-for="(box, index) in boxes" v-bind:key="index" :id="index">{{ box.name }}</Movable>
        </div>
    </div>
</template>

<script>
    import Movable from './Movable'
    import { mapState, mapMutations, mapGetters} from 'vuex'
    export default {
        name: "Map",
        computed:{
            ...mapState(['boxes','selected']),
            ...mapGetters(['selectedBox','mapName', 'mapUrl', 'mapWidth']),
            selectedId() {
                return this.$store.state.selected
            }
        },
        components: { Movable },
        data: () => {
            return {
                value: '',
                message:'',
                neighborMode: false,
                selectedTerrain: ''
            }
        },
        mounted(){
            this.$store.commit('clear');
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
              this.$store.commit('updateMapName', data.name);
              this.$store.commit('updateUrl', data.url);
              this.$store.commit('updateRev', data._rev);
          }).catch(errors => {
                console.log(errors);
          });
        },
        methods: {
            ...mapMutations(['clear','updateMapName']),
            debugMe(e){

            },
          addBox(){
              this.$store.commit('addBox')
          },
            updateName (e) {
                this.$store.commit('updateName', e.target.value)
            },
            updateMapName (e) {
                this.$store.commit('updateMapName', e.target.value)
            },
            updateTerrain (e) {
                this.$store.commit('updateTerrainType', e.target.value)
            },
            updateUrl (e) {
                this.$store.commit('updateUrl', e.target.value)
            },
            updateIsCity (e) {
                this.$store.commit('updateIsCity', e.target.checked)
            },
            updateWidth (e) {
                this.$store.commit('updateWidth', e.target.value)
            },
            saveMap(){
                const x= this.$store.state;
                const arg = {
                    _rev: x._rev,
                    name: this.mapName,
                    url: this.mapUrl,
                    boxes: [...x.boxes]
                }
                axios.put('/api/area-maps/'+this.$route.params.id, arg).then( response => {
                    // this.$router.push(response.data.links.self)
                }).catch(errors => {
                    console.log(errors);
                    // this.errors = errors.response.data.errors;
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .component-wrapper{
        margin-bottom:915px;
        .neighbor-mode-banner{
            display:flex;
            justify-content: center;
            padding: 5px 0;
            color:black;
            background-color: orange;
            font-size:20px;
        }
    }
    .meta-wrapper{
        width:400px;
        margin: auto;
        .flex-container{
            display:flex;
            justify-content: space-between;
        }

    }
    .map-wrapper {
        position: relative;
        widtH:1024px;
        .the-image {
            width: 1024px;
            position: static;
            left:0;
            top:0;
        }
    }

</style>
