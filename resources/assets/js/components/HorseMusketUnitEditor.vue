<template>
    <div  style="display:inline-block;">
    <button class="btn btn-xs" @click="show = !show">edit</button>
    <div class="panel"  v-if="show">
        <div class="close-row">
            <i @click="show = !show" class="close-symbol fa fa-times"></i>
        </div>
        <div class="row">
            <div class="left-col text-right col-xs-5">
                <label>
                    <span :class="[type,unit.nationality]" v-for="type in types" @click="unit.class=type">
                        <span class="counterWrapper">
                            <button  class="counter type-button"></button>
                        </span>
                    </span>
                    <input type="text" v-model="unit.class">
                    type
                </label>
                <label >
                <input v-model="unit.combat"> combat
                </label>

            </div>
            <div class="middle-col col-xs-2">
                <horse-musket-unit :unit="unit"></horse-musket-unit>
            </div>
            <div class="right-col col-xs-5">
                <label>range <input v-model="unit.range"></label>
                <label>movement <input v-model="unit.movement"></label>
                <label>nationality <input v-model="unit.nationality"></label>
                <button class="btn btn-xs btn-info" @click="unit.shadow = !unit.shadow "> shadow </button>
            </div>
        </div>
        <div v-for="key in nonStandardKeys" class="row">
            <div class="left-col col-xs-6" for="name" v-if="key !== 'reinforceTurn'">{{ key }}</div>
            <input type="checkbox" @click="iClick(unit)" v-model="unit[key]" v-if="key === 'deployed'">
            <input type="text"v-model="unit[key]" class="col-xs-6 right-col wide-input" v-if="key !== 'reinforceTurn' && key !== 'deployed'">
        </div>
        <div class="row " v-if="unit.reinforceTurn">
            <div class="left-col col-xs-6" for="name">Reinforce Turn</div>
            <input type="text"v-model="unit.reinforceTurn" class="col-xs-6 right-col wide-input"  >
        </div>
    </div>
    </div>
</template>

<script>
    import HorseMusketUnit from "./HorseMusketUnit.vue";
    export default {
        props: ["unit"],
        data()
        {
            return {
                show: false,
                nonStandardKeys: {},
                types: [
                    'infantry',
                    'cavalry',
                    'artillery',
                    'horseartillery',
                ]
            }
        },
        components:{
            horseMusketUnit: HorseMusketUnit
        }
    }
</script>

<style lang="scss" scoped>

    .close-row{
        text-align:right;
        float:none;
    }
    .close-symbol{
    }
    .panel{
        position:relative;
        padding:10px;
    }


    .type-button{
        margin: 0 5px 0 0;
        width:16px;
        height:16px;
        background-repeat:no-repeat;
    }
</style>