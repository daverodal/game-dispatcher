<template>
    <div  style="display:inline-block; width:50%;">
    <button class="btn btn-xs" @click="show = !show">edit</button>
    <div class="panel"  v-if="show">

        <div class="close-row">
            <i @click="show = !show" class="close-symbol fa fa-times"></i>
        </div>
        <div class="row">
            <div class="left-col col-xs-5">
                att <input v-model="unit.attack">
                def <input v-model="unit.defense">
                reinforce zone
                <select v-model="unit.reinforceZone">
                    <option disabled value="">Please select one</option>
                    <option>A</option>
                    <option>B</option>
                    <option>C</option>
                    <option>D</option>
                </select>


            </div>
            <div class="middle-col col-xs-2">
                <modern-tactical-unit :unit="unit"></modern-tactical-unit>
            </div>
            <div class="right-col col-xs-5">
                range <input v-model="unit.range"><br>
                movement <input v-model="unit.movement">
                weapons <input v-model="unit.weapons">
                target <input v-model="unit.target">
                unit class
                <select v-model="unit.class">
                    <option disabled value="">Please select one</option>
                    <option>inf</option>
                    <option>mech</option>
                    <option>armor</option>
                    <option>infantry</option>
                    <option>artillery</option>
                    <option>anti-tank</option>
                    <option>truck</option>
                    <option>apc</option>

                </select>
                ForceId
                <select v-model="unit.forceId">
                    <option disabled value="">Please select one</option>
                    <option>1</option>
                    <option>2</option>
                </select>
                Nationality
                <select v-model="unit.nationality">
                    <option disabled value="">Please select one</option>
                    <option>German</option>
                    <option>Soviet</option>
                    <option>American</option>
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

    import ModernTacticalUnit from './ModernTacticalUnit.vue';

    export default {
        props: ["unit"],
        components:{
            modernTacticalUnit: ModernTacticalUnit
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
</style>