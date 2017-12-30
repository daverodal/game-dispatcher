<template>
    <div class="unit-wrapper unit"  :class="[unit.nationality, unit.class]" :id="unit.id" alt="0">
        <div v-if="unit.class != 'supply'" class="unitSize">{{unit.unitSize}}</div>
        <div class="counterWrapper">
            <img  v-if="unit.class != 'supply' && unit.class != 'truck'" :src="'/assets/unit-images/'+unit.image" class="counter"><span class="unit-desig">{{unit.desig}}</span>
            <i v-if="unit.class === 'supply'" class="counter-symbol fa fa-adjust"></i>
            <i v-if="unit.class === 'truck'" class="counter-symbol fa fa-truck"></i>
            <span class="unit-desig">{{unit.id}}</span>
        </div>
        <div class="unit-numbers">{{unit.reduced ? unit.minCombat : unit.maxCombat }} - {{unit.movement}}</div>
    </div>
</template>

<script>

    export default {
        props: ["unit"],
        mounted(){
          if(!this.unit.num){
              Vue.set(this.unit, "forceId",  1)
              Vue.set(this.unit, "class",  'inf')
              Vue.set(this.unit, "movement",  5)
              Vue.set(this.unit, "maxCombat",  3)
              Vue.set(this.unit, "minCombat",  1)
              Vue.set(this.unit, "num",  1)
              Vue.set(this.unit, "range",  1)
              Vue.set(this.unit, "reinforce",  'A')
              Vue.set(this.unit, "reduced",  false)
              Vue.set(this.unit, "unitSize",  'xx')
              Vue.set(this.unit, "image",  'multiInf.png')
              if(!this.unit.nationality){
                  Vue.set(this.unit, "nationality", 'rebel')
              }
          }
        },
        methods:{
            canIDoThis(){
            }
        }

    }

</script>

<style lang="scss" scoped>


        .unit-wrapper{
            background-size:16px 16px;
            background-repeat:  no-repeat;
        }

        .corner{
            position:absolute;
            font-size:14px;
        }
        .unit .counterWrapper .counter-symbol {
            color: black;
            margin-left: 10px;
            display: block;
            font-size: 14px;
        }
        .unit.supply .counterWrapper {
            height:20px;
            .counter-symbol {
            color: black;
            margin-left: 6px;
            display: block;
            font-size: 21px;
        }
        }
        .unit-desig{
            font-size: 7px;
            position: absolute;
            left: 14px;
            top: 12px;
            -webkit-transform: rotate(-90deg);
            -webkit-transform-origin: center;
            width:30px;
            text-align: center;
        }
    .unit {
        height: 38px;
        color:black !important;
        font-size:14px;
            &.unit-wrapper{
                &.NapAllied{
                    background: lemonchiffon;
                }
            }
        .unit-numbers {
            font-size: 12px;
            font-weight: bold;
            height: 12px;
            font-size: 11px;
            text-align: center;
            color: black;
        }

         .counter {
            margin-top: -5px;
            width: 32px;
            margin-top: -4px;
        }

         .unitSize {
            height: 10px;
            font-size: 9px;
            text-align: center;
            color: black;
        }
        .counterWrapper{
            height: 10px;
            background-color:transparent !important;
            .counter{
                background-repeat: no-repeat;
            }
        }
    }

        .unit-wrapper {
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box;
            * {
                -webkit-box-sizing: content-box;
                -moz-box-sizing: content-box;
                box-sizing: content-box;
            }

            border: 3px solid;
            border-color: #ccc #333 #333 #ccc;
            display: inline-block;
            margin: 0 10px;
            width: 32px;
            height: 32px;
            position: relative;
            background-color: white;
            color: black;
        }
        .unit-num{
            display: inline-block;
            width:38px;
            height:38px;
        }

    .clear{
        clear:both;
    }
    @import "ModernMixins";

    $loyalistColor: #84b5ff;
    $loyalistGuardColor:#aaa;
    $rebelColor: #5c5;

    @include unitColor(rebel,  $rebelColor);
    @include unitColor(loyalist,$loyalistColor);
    @include unitColor(loyalGuards,$loyalistGuardColor);

    @include unitColor(soviet,  rgb(223, 88, 66));
    @include unitColor(prc,  rgb(247, 206, 0));
</style>