<template>
    <div  :style="{display:show ?  'block' :'inline-block' }">
    <button class="btn btn-xs" @click="show = !show">edit</button>
    <div class="panel"  v-if="show">
        <div class="close-row">
            <i @click="show = !show" class="close-symbol fa fa-times"></i>
        </div>
        <div class="row">
            <div class="middle-col col-xs-3">
                <horse-musket-unit :unit="unit"></horse-musket-unit>
            </div>
            <div class="right-col col-xs-9">
                <div class="row">
                    <label class="col-xs-2">type
                    </label>

                        <span :class="[type,unit.nationality]" v-for="type in types" @click="unit.class=type">
                            <span class="counterWrapper">
                                <button  class="counter type-button"></button>
                            </span>
                        </span>
                        <input type="text" v-model="unit.class">
                </div>
                <div class="row">
                    <label class="col-xs-2">combat</label>
                        <input class="col-xs-6" v-model="unit.combat">
                </div>
                <div class="row">
                    <label class="col-xs-2"> deployed</label> <input class="col-xs-8" type="checkbox" v-model="unit.deployed">
                </div>
                <div class="row">
                    <label class="col-xs-2">range</label><input class="col-xs-8" v-model="unit.range">
                </div>
                <div class="row">
                    <label class="col-xs-2">movement</label> <input class="col-xs-8" v-model="unit.movement">
                </div>
                <div class="row">
                    <label class="col-xs-2">nationality</label>
                    <select class="col-xs-2" v-model="unit.nationality">
                        <option v-for="nat in scenario.nationalities">{{nat}}</option>
                    </select>
                    <input class="col-xs-6" v-model="unit.nationality">
                </div>
                <div class="row">
                    <label  class="col-xs-2">reinforce</label>
                    <input class="col-xs-8" v-model="unit.reinforce">
                </div>
                <div class="row">
                    <label  class="col-xs-2">Reduced Value</label>
                    <input class="col-xs-8" v-model="unit.reduced">
                </div>
                <div  v-if="!unit.deployed" class="row">
                    <label class="col-xs-2">reinforce turn</label><input class="col-xs-8" v-model="unit.reinforceTurn">
                </div>
                <button class="btn btn-xs btn-info" @click="unit.shadow = !unit.shadow "> shadow </button>
            </div>
        </div>
        <div v-for="key in nonStandardKeys" class="row">
            <div class="left-col col-xs-6" for="name" v-if="key !== 'reinforceTurn'">{{ key }}</div>
            <input type="checkbox" @click="iClick(unit)" v-model="unit[key]" v-if="key === 'deployed'">
            <input type="text"v-model="unit[key]" class="col-xs-6 right-col wide-input" v-if="key !== 'reinforceTurn' && key !== 'deployed'">
        </div>

        {{ unit }}
    </div>
    </div>
</template>

<script>
    import HorseMusketUnit from "./HorseMusketUnit.vue";
    export default {
        props: ["unit", "scenario"],
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