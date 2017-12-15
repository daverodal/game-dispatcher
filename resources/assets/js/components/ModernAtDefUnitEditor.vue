<template>
    <div  style="display:inline-block; width:50%;">
    <button class="btn btn-xs" @click="show = !show">edit</button>
    <div class="panel"  v-if="show">

        <div class="close-row">
            <i @click="show = !show" class="close-symbol fa fa-times"></i>
        </div>
        <div class="row">
            <div class="left-col col-xs-5">
                <button class="type-button" v-for="type in types" @click="unit.image=type"> <img :src="'/assets/unit-images/' + type"></button>
                <input class="unit-class" v-model="unit.image"> type<br>
                <input v-model="unit.attack"> att
                <input v-model="unit.defense"> def
                <input v-model="unit.unitSize"> unitSize



            </div>
            <div class="middle-col col-xs-2">
                <modern-at-def-unit :unit="unit"></modern-at-def-unit>
            </div>
            <div class="right-col col-xs-5">
                movement <input v-model="unit.movement">
                <select v-model="unit.class">
                    <option disabled value="">Please select one</option>
                    <option>inf</option>
                    <option>mech</option>
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

    import ModernAtDefUnit from './ModernAtDefUnit.vue';

    export default {
        props: ["unit"],
        components:{
            modernAtDefUnit: ModernAtDefUnit
        },
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
        },
        components:{
            ModernAtDefUnit
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