import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
let state = {
  name: 'me',
  _rev: '',
  url: 'http://davidrodal.com',
  width: 1024,
  boxes: [],
    selected: null
};
// const jstate = JSON.parse(localStorage.getItem('state'))
// if(jstate){
//   state = jstate;
// }
export default new Vuex.Store({
  state: {
      ...state
  },
  mutations: {
    addBox(state){
      const id = state.boxes.length;
      state.boxes.push({id: id, x: 0, y: 0, isCity: false, terrainType: "", name: '', neighbors: []});
        state.selected = id;
        localStorage.setItem('state', JSON.strinxgify(state));
      },
    createBox(state, payload){
      state.boxes.push(payload);
      state.selected = payload.id;
      state.isCity = payload.isCity;
      state.terrainType = payload.terrainType;
      localStorage.setItem('state', JSON.stringify(state));
    },
      moveBox(state, payload){
        state.boxes[payload.id].x = payload.x;
        state.boxes[payload.id].y = payload.y;
        state.selected = payload.id;
        localStorage.setItem('state', JSON.stringify(state));

      },
      selectBox(state, payload){
        state.selected = payload;
        localStorage.setItem('state', JSON.stringify(state));
      },
      clear(state){
        state.selected = null;
        state.boxes = [];
        state.name = '';
        state.url = '';
        localStorage.setItem('state', JSON.stringify(state));
      },
      updateName(state, payload, commit){
      debugger;
        state.boxes[state.selected].name = payload;
      },
      updateIsCity(state, payload, commit){
      debugger;
        state.boxes[state.selected].isCity = payload;
      },
      updateTerrainType(state, payload){
      debugger;
        state.boxes[state.selected].terrainType = payload;
      },
    updateMapName(state, payload){
        state.name = payload;
      },
    updateUrl(state, payload){
      state.url = payload;
    },
    updateRev(state, payload){
      state._rev = payload;
    },
    updateWidth(state, payload){
      state.width = payload;
    },
    toggleNeighbor(state, payload){
      const box = state.boxes[state.selected];
      const found = box.neighbors.find((id) =>{
        return id === payload;
      });
      if(typeof found !== "undefined"){
        /* found one, remove it */
        const newNeighbors = box.neighbors.filter( id => id != payload);
        state.boxes[state.selected].neighbors = [...newNeighbors];
        const otherBox = state.boxes[payload];
        const otherNeighbors = otherBox.neighbors.filter(id => id != state.selected);
        state.boxes[payload].neighbors = [...otherNeighbors];
      }else{
        /* not found, add it */
        state.boxes[state.selected].neighbors = [...state.boxes[state.selected].neighbors, payload ];
        state.boxes[payload].neighbors = [...state.boxes[payload].neighbors, state.selected];
      }
      }
  },
    getters: {
      selectedBox(state){
        debugger;
        return state.boxes[state.selected];
      },
      mapName(state){
        return state.name;
      },
      mapUrl(state){
        return state.url;
      },
      mapWidth(state){
        return state.width;
      },
      selectedBoxNeighbors(state){
        return state.boxes[state.selected].neighbors;
      }
    },
  actions: {
  },
  modules: {
  }
})
