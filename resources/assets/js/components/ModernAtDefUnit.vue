<template>
    <div class="unit-wrapper unit"  :class="[unit.nationality]" :id="unit.id" alt="0">
        <div class="shadow-mask" :class="{shadowy: unit.shadow}"></div>
        <div class="unitSize">{{unit.unitSize}}</div>
        <div class="counterWrapper">
            <img  :src="'/assets/unit-images/'+unit.image" class="counter"><span class="unit-desig">{{unit.desig}}</span>
        </div>
        <div class="unit-numbers">{{attack}}-{{defense}}-{{unit.movement}}</div>
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
                Vue.set(this.unit, "maxCombat",  5)
                Vue.set(this.unit, "maxDefense",  3)
                Vue.set(this.unit, "minCombat",  2)
                Vue.set(this.unit, "minDefense", 1)
                Vue.set(this.unit, "num",  1)
                Vue.set(this.unit, "range",  1)
                Vue.set(this.unit, "reinforce",  'A')
                Vue.set(this.unit, "reduced",  false)
                Vue.set(this.unit, "unitSize",  'xx')
                Vue.set(this.unit, "image",  'multiInf.png')
                if(!this.unit.nationality){
                    Vue.set(this.unit, "nationality", 'indian')
                }
            }
        },
        computed:{
            attack(){

                if(this.unit.reduced){
                    return this.unit.minCombat;
                }
                return this.unit.maxCombat;
            },
            defense(){
                if(this.unit.reduced){
                    return this.unit.minDefense;
                }
                return this.unit.maxDefense;
            }
        }
    }

</script>

<style lang="scss" scoped>
    @import "ModernMixins";

    $loyalistColor: #84b5ff;
    $loyalistGuardColor:#aaa;
    $rebelColor: #5c5;
    $indianColor: #ff9933;

    $pakistaniColor: #02b500;

    @include unitColor(indian,  $indianColor);
    @include unitColor(pakistani,$pakistaniColor);


    .unit {
        height: 38px;
        color:black !important;
        font-size:14px;

        &.unit-wrapper{
                background-size:16px 16px;
                background-repeat:  no-repeat;
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
                color: black;
                &.NapAllied{
                    background: pink;
                }
            &.big{
                height:64px;
                width:64px;
                font-size:28px;
                .unitSize{
                    height: 20px;
                    font-size: 18px;
                }
                .counterWrapper{
                    height:20px;
                    .hq{
                        width:16px;
                        line-height:8px;
                        top: -6px;
                        left:30px;
                        font-size:32px;
                    }
                    .counter{
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

        }
        .unit-numbers {
            font-size: 12px;
            font-weight: bold;
            height: 12px;
            font-size: 11px;
            text-align: center;
            color: black;
            letter-spacing:-1px;
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
    }

        .unit-wrapper {

        }


</style>