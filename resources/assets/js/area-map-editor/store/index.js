import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
let state = {
  name: 'me',
  url: 'http://davidrodal.com',
  width: 1024,
  boxes: [],
    selected: null
};
// const jstate = JSON.parse(localStorage.getItem('state'))
// debugger;
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
      state.boxes.push({id: id, x: 0, y: 0, name: ''})
        state.selected = id;
        localStorage.setItem('state', JSON.stringify(state));
      },
    createBox(state, payload){
      state.boxes.push(payload);
      state.selected = payload.id;
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
        state.boxes[state.selected].name = payload;
      },
      updateMapName(state, payload){
        state.name = payload;
      },
    updateUrl(state, payload){
      state.url = payload;
    },
    updateWidth(state, payload){
      debugger;
      state.width = payload;
    }
  },
    getters: {
      selectedBox(state){
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
      }
    },
  actions: {
  },
  modules: {
  }
})
