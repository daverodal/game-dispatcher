<template>
    <div  style="display:inline-block; width:50%;">
    <button class="btn btn-xs" @click="show = !show">edit</button>
    <div class="panel"  v-if="show">

        <div class="close-row">
            <i @click="show = !show" class="close-symbol fa fa-times"></i>
        </div>
        <div class="row">
            <div class="left-col col-xs-5">
                <button  class="type-button" v-for="type in types" @click="unit.image=type"> <img :src="'/assets/unit-images/' + type"></button>
                <input class="unit-class" v-model="unit.image"> type<br>
                <input v-model="unit.maxCombat"> max combat

                <input v-model="unit.minCombat"> min combat


            </div>
            <div class="middle-col col-xs-2">
                <modern-unit :unit="unit"></modern-unit>
            </div>
            <div class="right-col col-xs-5">
                deployed <input v-model="unit.deployed" type="checkbox">
                movement <input v-model="unit.movement">
                unitSize <input v-model="unit.unitSize">
                nationality <input v-model="unit.nationality">
                <select v-model="unit.nationality">
                    <option v-for="nat in scenario.nationalities ">{{nat}}</option>
                </select>
                force id <input v-model="unit.forceId">
                reinforce <input v-model="unit.reinforce">
                reduced <input v-model="unit.reduced" type="checkbox">

                <select v-model="unit.class">
                    <option disabled value="">Please select one</option>
                    <option>inf</option>
                    <option>para</option>
                    <option>shock</option>
                    <option>mountain</option>
                    <option>heavy</option>
                    <option>mech</option>
                    <option>supply</option>
                    <option>truck</option>
                </select>
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
    export default {
        props: ["unit", "scenario"],
        data()
        {
            return {
                show: false,
                nonStandardKeys: {},
                types: [
                    'multiInf.png',
                    'multiArmor.png',
                    'multiMech.png',
                    'multiArt.png',
                    'multiCav.png',
                    'multiGlider.png',
                    'multiGor.png',
                    'multiHeavy.png',
                    'multiMotArt.png',
                    'multiMotInf.png',
                    'multiMotMt.png',
                    'multiMountain.png',
                    'multiPara.png',
                    'multiRecon.png',
                    'multiRecon1.png',
                    'multiShock.png'
                ]
            }
        }
    }
</script>

<style lang="scss" scoped>

    .close-symbol{
        float:right;
    }
    .panel{
        position:relative;
        padding:20px;
    }


    .type-button{
        img{
            width:32px;
        }
        padding:0;
        height:20px;
        overflow:hidden;
    }
</style>