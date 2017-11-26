<template>
    <div class="unit-wrapper unit"  :class="[unit.nationality]" :id="unit.id" alt="0">
        <div class="unitSize">{{unit.unitSize}}</div>
        <div class="counterWrapper">
            <img  :src="'http://localhost:8080/assets/unit-images/'+unit.image" class="counter"><span class="unit-desig">{{unit.desig}}</span>
        </div>
        <div class="unit-numbers">{{unit.attack}} - {{unit.defense}} - {{unit.movement}}</div>
    </div>
</template>

<script>

    export default {
        props: ["unit"]

    }

</script>

<style lang="scss" scoped>

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
                background-color: white;
                color: black;
                &.prc{
                    background: pink;
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
            .counter{
                background-repeat: no-repeat;
            }
        }
    }

        .unit-wrapper {

        }


</style>