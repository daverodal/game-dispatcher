<template>
  <Moveable
    class="moveable"
    v-bind="moveable"
    @drag="handleDrag"
    @resize="handleResize"
    @scale="handleScale"
    @rotate="handleRotate"
    @warp="handleWarp"
    @click="clickme"
    @dragStart="handleDragStart"
    @render="renderme"
    @dragEnd="handleDragEnd"
    :style="{left: left, top: top}"
    :class="{}"
  >
    <slot></slot>
  </Moveable>
</template>

<script>
import Moveable from 'vue-moveable'
import {mapGetters} from "vuex";

export default {
  name: 'MovableBorderBox',
  components: {
    Moveable
  },
    props:[ 'id' ]
    ,
  data: () => ({
    moveable: {
      draggable: true,
      throttleDrag: 0,
      resizable: false,
      throttleResize: 1,
      keepRatio: true,
      scalable: false,
      throttleScale: 0,
      rotatable: false,
      throttleRotate: 3
    }
  }),
  mounted(){
    console.log(this.neighborMode);
  },
    computed:{
        ...mapGetters(['neighborMode']),
      left() {
          console.log(this.id);
          return this.$store.state.borderBoxes[this.id].x + "px";
      },
        top() {
            return this.$store.state.borderBoxes[this.id].y + "px";
        },
        selected() {

          return this.$store.state.selected === this.id;
        },
        neighbor(){
          const neighbors = this.$store.getters.selectedBoxNeighbors;
          const found = neighbors.find((ele) =>{
              return ele === this.id;
            });
          if(typeof found !== "undefined") {
            return true;
          }
           return false
        }
    },
  methods: {
      handleDragEnd(e){
        console.log('start')

      },
      handleDragStart(e){
console.log("end");
      },
      clickme(){
      },
      renderme(){
      },


    handleDrag ({ target, left, top }) {
        console.log("Left  "+ left + " top "+ top);
        console.log(this.id);
        console.log(target);
        this.$store.commit('selectedBorderBox', this.id);
        this.$store.commit('moveBorderBox',{id: this.id, x: left, y: top});
      // target.style.left = `${left}px`
      // target.style.top = `${top}px`
    },
    handleResize ({ target, width, height, delta }) {
      delta[0] && (target.style.width = `${width}px`)
      delta[1] && (target.style.height = `${height}px`)
    },
    handleScale ({ target, transform, scale }) {
      target.style.transform = transform
    },
    handleRotate ({ target, dist, transform }) {
      target.style.transform = transform
    },
    handleWarp ({ target, transform }) {
      target.style.transform = transform
    }
  }
}
</script>

<style lang="scss" scoped>
  .moveable {
    font-family: "Roboto", sans-serif;
    position: absolute;
    width: auto;
    height: auto;
    text-align: center;
    font-size: 16px;
    margin: 0 auto;
    font-weight: 100;
    letter-spacing: 1px;
    background: transparent;

    border: none;
    img{
      margin-top: -8px;
      margin-left: -8px''
    }
    .target-img{
      margin-left:-300px;
    }
  }
  /*
   * should be a better way of doing this.
   */
  .moveable-control-box{
    display:none !important;
  }
</style>
