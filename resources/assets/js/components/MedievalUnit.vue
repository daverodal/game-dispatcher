<template>


    <div :id="unit.id" :style="unit.style" class="unit rel-unit"
         :class="[unit.nationality, unit.class]">
        <div v-show="unit.oddsDisp" class="unitOdds" :class="unit.oddsColor">{{ unit.oddsDisp }}</div>
        <div class="shadow-mask" :class="{shadowy: unit.shadow}"></div>
        <div class="counterWrapper">
            <div v-if="unit.bow" class="bow" style=""></div>
            <div v-if="unit.hq" class="hq"> {{ "......".slice(0, unit.commandRadius) }} </div>
            <div v-if="!unit.hq" class="counter"></div>
            <i v-if="unit.hq" class="fa fa-flag"></i>
        </div>
        <div class="range"> {{ unit.armorClass }}</div>

        <!--<img v-for="arrow in unit.arrows" :style="arrow.style" class="counter arrow"-->
             <!--:src="url('assets/unit-images/short-red-arrow-md.png')">-->

        <div :class="unit.infoLen" class="unit-numbers"> {{ unitNumbers }}</div>
        <div class="unit-steps"> {{ "...".slice(0, unit.steps) }}</div>
        <i v-show="!unit.command" class="fa fa-star unit-command" aria-hidden="true"></i>

    </div>





</template>

<script>
/*

    <div :class="[unit.class,unit.nationality]" class="unit unit-wrapper" :id="unit.id" alt="0">
        <div class="shadow-mask" :class="{shadowy: unit.shadow}"></div>
        <div class="counterWrapper">
            <div class="counter"></div>
        </div>
        <div v-if="unit.range > 1" class="range">{{unit.range}}</div>
        <div class="unit-numbers">{{unit.combat}} - {{unit.movement}}</div>
    </div>
 */
    export default {
        props: ["unit"],
        computed:{
            unitNumbers(){
                return this.unit.combat + " B " + this.unit.movement;
            }
        },
        mounted(){
            if(!this.unit.num){
                Vue.set(this.unit, "forceId",  1)
                Vue.set(this.unit, "class",  'cavalry')
                Vue.set(this.unit, "combat",  5)
                Vue.set(this.unit, "movement",  5)
                Vue.set(this.unit, "num",  1)
                Vue.set(this.unit, "range",  1)
                Vue.set(this.unit, "reinforce",  'A')
                Vue.set(this.unit, "reinforceTurn",  '1')
                Vue.set(this.unit, "reduced",  false)
                Vue.set(this.unit, "armorClass",  'K')
                Vue.set(this.unit, "deployed",  true)
                Vue.set(this.unit, "command",  false)
                Vue.set(this.unit, "hq",  false)
                Vue.set(this.unit, "bow",  false)
                Vue.set(this.unit, "facing",  1)
                Vue.set(this.unit, "steps",  1)
                Vue.set(this.unit, "commandRadius",  1)

                Vue.set(this.unit, "image",  'multiInf.png')
                if(!this.unit.nationality){
                    Vue.set(this.unit, "nationality", 'rebel')
                }
            }
        }

    }

</script>

<style lang="scss" scoped>
    * {
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
    }

    .fa-flag {
        display: inline-block;
        width:32px;
        border-bottom: 1px solid black;
    }
    .counterWrapper{

    }
        .unit-wrapper{
            background-size:16px 16px;
            background-repeat:  no-repeat;
        }

        .corner{
            position:absolute;
            font-size:14px;
        }


    .unit {
        position:relative;
        display: inline-block;
        vertical-align: middle;
        line-height:1;
        margin: 0 10px;

        height: 32px;
        width:32px;
        font-size:14px;

        &.big{
            height:64px;
            width:64px;
            font-size:28px;
            .counterWrapper{
                .hq{
                    width:16px;
                    line-height:8px;
                    top: -6px;
                    left:30px;
                    font-size:32px;
                 }
                .counter{
                    height:28px;
                    border-bottom: 2px solid black;
                    width:64px;
                    background-size:28px !important;
                }
                .bow{
                    left: 26px;
                    height: 26px;
                    background-size: 20px 26px;
                    width: 20px;
                }
                .fa-flag{
                    width:64px;
                    border-bottom: 2px solid black;
                }

            }

            .range{
                font-size: 24px;
            }
            .unit-numbers{
                font-size:22px;
                height: 24px;
            }
            .unit-steps{
                margin-top:-20px;
            }
            .unit-command{
                font-size:12px;
            }
        }


        border: 3px solid;
        border-color: #ccc #333 #333 #ccc;


            &.unit-wrapper{
                &.prc{
                    background: lemonchiffon;
                }
            }
        .range{
            top: 0px;
            right:0px;
            position: absolute;
            font-size:12px;
        }
        .unit-numbers {
            font-size: 11px;
            font-weight: 400;
            height: 12px;
            text-align: center;
            font-family: Helvetica, Arial, SansSerif;
        }
        .unit-steps{
            margin-top: -10px;
            text-align:center;
        }

         .counter {
            width: 32px;
        }

         .unitSize {
            height: 10px;
            font-size: 9px;
            text-align: center;
        }

        .counterWrapper{
            position:relative;

            .hq {
                word-wrap: break-word;
                width: 8px;
                line-height: 4px;
                top: -2px;
                position: absolute;
                left: 15px;
                font-size: 16px;
            }




            .counter{
                background-repeat: no-repeat;
                height: 14px;
                border-bottom: 1px solid black;
            }
            .bow{
                position:absolute;
                left: 13px;
                height: 13px;


                background: url('../images/arrow.svg');
                background-size:10px 13px;
                width:10px;
            }
        }
        .shadow-mask {
            top: 0px;
            left: 0px;
            height: 100%;
            width: 100%;
            position: absolute;
            background: transparent;
            z-index: 1;
            &.red-shadowy {
                height: 50%;
            }
            &.shadowy {
                background: rgba(0,0,0,.3);
            }
        }
        .unit-command{
            position: absolute;
            background: transparent;
            bottom: 0px;
            font-size: 6px;
        }
    }

        .unit-wrapper {
            border: 3px solid;
            border-color: #ccc #333 #333 #ccc;
            display: inline-block;
            margin: 0 10px;
            width: 32px;
            height: 32px;
            position: relative;
            background-color: white;
        }
        .unit-num{
            display: inline-block;
            width:38px;
            height:38px;
        }

    .clear{
        clear:both;
    }


    $badge-size: 14px;
    $vintage-badge-size:19px;
    $unit-numbers-size:16px;
    $vintage-unit-numbers-size:10px;


    @import "MedievalMixins";
    $crusaderColor: #CE0323;
    $turkishGreen: #11943A;
    $turkishOrange: #FFC835;

    $lombardColor: #60b2ff;
    $normanColor: #FFA99B;
    @include unitColor(norman,#FFA99B,  Brit, red );
    @include unitColor(lombard, $lombardColor, Bavarian, #60b2ff, black, black);
    @include unitColor(swabian, $lombardColor,  Bavarian, #fb001d, black, black );

    @include unitColor(turkish, $turkishGreen,  Turk, $turkishOrange, black, white );
    @include unitColor(crusader, $crusaderColor,  Crusader, white, black, white );

</style>