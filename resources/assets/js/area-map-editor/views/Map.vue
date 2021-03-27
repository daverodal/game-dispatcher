<template>
    <div class="component-wrapper">

      <flash-message class="myCustomClass"></flash-message>

      <a  class="btn btn-danger" href="#" @click="$router.back()">
            < Back
        </a>
        You have {{boxes.length}} boxes. Map Name is "{{ mapName }}"
      <div class="flex-container">
      <div class="col-sm-6 meta-wrapper">
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
          <div  class="flex-container">
            <label for="width">Game Name {{ gameName }} f</label>
            <input type="text" v-model="gameName" class="text-gray-900 pt-8 w-full"
                   placeholder="Game Name" @input="updateGameName" >
          </div>
          <div  class="flex-container">
            <label for="width">Map Width</label>
            <input type="text" v-model="scenarioName" class="text-gray-900 pt-8 w-full"
                   placeholder="Scenario Name" @input="updateScenarioName" >
          </div>
        </div>

        <div class="col-sm-6 meta-wrapper" v-if="selected !== null">
            id: {{ selectedId }} <br>
            x: {{ selectedBox.x }}<br>
            y: {{ selectedBox.y }}<br>
            Name: <input type="text" :value="selectedBox.name" @input="updateName"  placeholder="enter name">
            TerrainType:
            <select v-model="terrainType">
                <option>forest</option>
                <option>water</option>
                <option>mountain</option>
                <option>pasture</option>
                <option>field</option>
                <option>desert</option>
            </select>
            Has a City?:<input type="checkbox" :checked="selectedBox.isCity" @input="updateIsCity">
            <p>
                Name is: {{ selectedBox.name }}
            </p>
          <div v-if="selectedBox && selectedBox.neighbors">
            <span v-for="neighbor in selectedBox.neighbors">
                {{neighbor}}
            </span>
          </div>
        </div>

      </div>
      <button @click="toggleNeighborMode">Neighbor mode</button>
      <button @click="toggleBorderMode">Show Border Boxes</button>
      <button @click="addBox">add box</button>
      <button @click="saveMap">Save</button>
      <button class="btn btn-primary" @click="publishMap">Publish</button>
      <div v-if="$store.state.selectedBorderBox !== null">
         Last moved borderbox {{$store.state.borderBoxes[$store.state.selectedBorderBox].key }}
        {{$store.state.borderBoxes[$store.state.selectedBorderBox].x }}
        {{$store.state.borderBoxes[$store.state.selectedBorderBox].y }}
      </div>
      <div v-if="neighborMode" class="neighbor-mode-banner">
          Neighbor Mode Enabled,
      </div>
        <div :style="{width: mapWidth+'px'}" class="map-wrapper">
            <img class="the-image" :style="{width: mapWidth+'px'}" :src="mapUrl" alt="map">
            <span v-for="(box, index) in borderBoxes"> {{box.x}} {{ box.y}} {{ index }}</span>


            <MovableBorderBox v-if="showBorderBoxes" v-for="(box, index) in borderBoxes" v-bind:key="box.key" :id="index"><img class="target-img" src="../../../images/Target2.svg"></MovableBorderBox>
            <Movable v-for="(box, index) in boxes" v-bind:key="index" :id="index">{{ box.name }}</Movable>
        </div>
      <button @click="toggleNeighborMode">Neighbor mode</button>
      <button @click="toggleBorderMode">Show Border Boxes</button>
      <button @click="addBox">add box</button>
      <button @click="saveMap">Save</button>
      <button class="btn btn-primary" @click="publishMap">Publish</button>
      <div v-if="neighborMode" class="neighbor-mode-banner">
        Neighbor Mode Enabled,
      </div>

    </div>
</template>

<script>
require('vue-flash-message/dist/vue-flash-message.min.css');
    import Movable from './Movable'
    import MovableBorderBox from './MovableBorderBox'
    import { mapState, mapMutations, mapGetters} from 'vuex'
    export default {
        name: "Map",
        computed:{
            ...mapState(['boxes','selected', 'borderBoxes', 'gameName', 'scenarioName']),
            ...mapGetters(['selectedBox','mapName', 'mapUrl', 'neighborMode', 'getMapWidth']),
            selectedId() {
                return this.$store.state.selected
            },
            myBorderBox(){
              return this.$store.state.borderBoxes
            },
            terrainType:{
                get(){
                    return  this.selectedBox.terrainType;
                },
                set(value){
                    this.$store.commit('updateTerrainType', value)
                }
            },
            mapWidth:{
                get(){
                    return this.getMapWidth;
                },
                set(value){
                  this.$store.commit('updateWidth', value);
                }
            }
        },
        components: { Movable , MovableBorderBox},
        data: () => {
            return {
                value: '',
                message:'',
                selectedTerrain: '',
              showBorderBoxes: true
            }
        },
        mounted(){
          this.$store.commit('clear');
          axios.get('/api/area-maps/'+ this.$route.params.id).then(response => {
            this.flash('Data loaded', 'success', {
              timeout: 2000,
            });
            const data = response.data;
              if(data.boxes && data.boxes.length > 0){
                  data.boxes.forEach(box => {
                    if(typeof box.isCity === "undefined"){

                      box.isCity = false;
                    }
                      if(!box.neighbors){
                          box.neighbors = [];
                      }
                      this.$store.commit('createBox', box);
                  })

              }

            if(data.borderBoxes && data.borderBoxes.length > 0){
              data.borderBoxes.forEach(box => {
                this.$store.commit('createBorderBox', box);
              })

            }
              // this.mapWidth = data.width;
              this.$store.commit('updateWidth', data.width);
              this.$store.commit('updateMapName', data.name);
              this.$store.commit('updateUrl', data.url);
              this.$store.commit('updateRev', data._rev);
              this.$store.commit('updateGameName', data.gameName);
              this.$store.commit('updateScenarioName', data.scenarioName);
          }).catch(errors => {
                console.log(errors);
          });
        },
        methods: {
            ...mapMutations(['clear','updateMapName', 'toggleNeighborMode', 'updateWidth']),
            debugMe(e){

            },
          publishMap(){
            axios.get('/wargame/terrainInit/'+ this.$store.state.gameName+ '/' + this.$store.state.scenarioName + '/' +
                this.$route.params.id).then( response => {
              // this.$router.push(response.data.links.self)
            });
        },
          toggleBorderMode(){
            this.showBorderBoxes = !this.showBorderBoxes;
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
          updateGameName (e) {
            this.$store.commit('updateGameName', e.target.value)
          },
          updateScenarioName (e) {
            this.$store.commit('updateScenarioName', e.target.value)
          },
            saveMap(){
                const x= this.$store.state;
                const arg = {
                    _rev: x._rev,
                    name: this.mapName,
                    url: this.mapUrl,
                    boxes: [...x.boxes],
                    borderBoxes: [...x.borderBoxes],
                    width: this.mapWidth,
                    gameName: this.$store.state.gameName,
                    scenarioName: this.$store.state.scenarioName
                }
                axios.put('/api/area-maps/'+this.$route.params.id, arg).then( response => {
                    // this.$router.push(response.data.links.self)
                  this.flash('Map Saved', 'success', {
                    timeout: 2000,
                  });
                }).catch(errors => {
                    console.log(errors);
                  this.flash('error saving map', 'error', {
                  });
                    // this.errors = errors.response.data.errors;
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .myCustomClass{
      position: absolute;
      width: 100%;
      z-index:3;
      top:0px;
    }
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
    .target-img{
      margin-left:-8px;
      margin-top: -8px;
      width:17px;
      height:17px;
    }
    .meta-wrapper{
        .flex-container{
            display:flex;
            justify-content: space-between;
        }

    }
    .map-wrapper {
        position: relative;
        width:auto;
        .the-image {
            width: 1024px;
            position: static;
            left:0;
            top:0;
        }
    }
    .flex-container{
      display:flex;
      align-items: stretch;
    }

</style>
