<template >

    <div>
        <slot></slot>
        <div class="panel">
            <slot name="content"></slot>
        </div>
        <div class="list-container">
            <div  v-for="(unit, key) in units" class="modern-unit-list">

                <component :is="unitType" :unit="unit"></component>
                <div class="unit-num">x {{ unit.num }}</div>
                <button class="btn btn-xs" @click="add(unit)">+</button>
                <button class="btn btn-xs" @click="remove(unit)">-</button>
                <button class="btn btn-xs" @click="clone(unit)">Clone</button>
                <component :is="unitType + '-editor'" :scenario="scenario" :unit="unit"></component>
            </div>
        </div>
    </div>

</template>

<script>

    import ModernAltDefUnit from './ModernAtDefUnit.vue'
    import ModernUnit from './ModernUnit.vue';
    import ModernTacticalUnit from './ModernTacticalUnit.vue';
    import ModernTacticalUnitEditor from './ModernTacticalUnitEditor.vue';
    import HorseMusketUnit from './HorseMusketUnit.vue';
    import HorseMusketUnitEditor from './HorseMusketUnitEditor.vue';
    import MedievalUnit from './MedievalUnit.vue';
    import MedievalUnitEditor from './MedievalUnitEditor.vue';
    ;



    export default {
        data(){
            return {
//              unitType: 'horse-musket-unit'
            }
        },
        components:{
            modernAtDefUnit: ModernAltDefUnit,
            modernUnit: ModernUnit,
            modernTacticalUnit: ModernTacticalUnit,
            modernTacticalUnitEditor: ModernTacticalUnitEditor,
            horseMusketUnit: HorseMusketUnit,
            horseMusketUnitEditor: HorseMusketUnitEditor,
            medievalUnit: MedievalUnit,
            medievalUnitEditor: MedievalUnitEditor
        },
        props:['unit', 'units', 'unit-type', 'scenario'],
        methods:{
            add(unit){
                unit.num++;
            },
            remove(unit) {
                if (unit.num > 0) {
                    unit.num--;
                }
            },
            clone(unit) {
                this.units.push(_.clone(unit));
            }
        }
    }

</script>

<style lang="scss" scoped>
    .list-container{
        padding: 10px 0;
    }
    .small{
        color: #898989;
        font-size:70%;
        margin-bottom:0;
        font-style: italic;
    }
    .panel{
        padding:0px 10px;
        text-align: center;
    }
    .modern-unit-list{
        padding:3px 0;
        font-size:14px;

        .unit-num{
            display: inline-block;
            width:38px;
        }
    }
    .clear{
        clear:both;
    }
    .unit-wrapper{
        vertical-align: middle;
    }

</style>