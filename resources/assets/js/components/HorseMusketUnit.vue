<template>
    <div :class="[unit.class,unit.nationality]" class="unit unit-wrapper" :id="unit.id" alt="0">
        <div class="counterWrapper">
            <div class="counter"></div>
        </div>
        <div v-if="unit.range > 1" class="range">{{unit.range}}</div>
        <div class="unit-numbers">{{unit.combat}} - {{unit.movement}}</div>

    </div>
</template>

<script>

    export default {
        props: ["unit"]

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


    .unit {
        height: 38px;
        color:black !important;
        font-size:14px;
            &.unit-wrapper{
                &.prc{
                    background: lemonchiffon;
                }
            }
        .range{
            top: 0px;
            right:3px;
            position: absolute;
            font-size:13px;
        }
        .unit-numbers {
            font-size: 13px;
            font-weight: 400;
            height: 12px;
            text-align: center;
            font-family: Helvetica, Arial, SansSerif;
        }

         .counter {
            width: 32px;
        }

         .unitSize {
            height: 10px;
            font-size: 9px;
            text-align: center;
            color: black;
        }

        .counterWrapper{
            height:14px;
            border:1px solid black;
            .counter{
                background-repeat: no-repeat;
                height: 14px;
                width: 14px;
                border-right: 1px solid black;
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



</style>