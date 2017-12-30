<template>
    <div class="coolBox">
        <h1>Scenario Editor (vue)</h1>
        <div v-if="scenario && scenario.unit && scenario.units[0]">
            {{ scenario.units[0].num }}
        </div>
        <div>

            <div class="form-group">
                <label for="name">Short Description</label>
                <input type="text" v-model="scenario.description" class="form-control" id="name">
            </div>
            <div class="form-group">
                <label for="longname">Long Description</label>
                <textarea type="text" v-model="scenario.longDescription" class="form-control" id="longname"></textarea>
            </div>
            <div class="form-group">
                <label for="longname">Unit Component</label>
                <select class="btn-xs" v-model="scenario.unitComponent">
                    <option>medieval-unit</option>
                    <option>horse-musket-unit</option>
                    <option>modern-at-def-unit</option>
                    <option>modern-unit</option>
                    <option>
                        modern-tactical-unit
                    </option>
                </select>
            </div>
            <button @click="addProperty"><i class="fa fa-plus"></i></button>
            <input v-if="addKey" v-model="newKey">
            <input v-if="addKey" v-model="newValue">
            <button v-if="addKey" @click="saveProperty()">Save</button>
            <div v-for="key in nonStandardKeys" class="form-group">
                <label for="keyName">
                    {{ key }}   <button @click="deleteProperty(key)"><i class="fa fa-times red"></i></button>
                    <input type="text" v-model="scenario[key]" class="form-control" id="keyName">
                </label>
            </div>

            <div>
                <button class="btn btn-primary" @click="publish">Publish</button>
                <button class="btn btn-danger" @click="cancel">Cancel</button>
                <button @click="newUnit" class="btn btn-success">New Unit</button>
            </div>
            <div>
                <units-list :scenario="scenario" :unit-type="scenario.unitComponent" :units="units">
                </units-list>
            </div>
        </div>
         <span v-for="value in scenario.multiValue">{{ value }}</span> {{scenario.multiValue }}
        <button class="btn btn-primary" @click="publish">Publish</button>
        <button class="btn btn-danger" @click="publish">Cancel</button>
    </div>
</template>

<script>

    export default {

        data() {
            return {
                static: 'i am unchanging',
                nonStandardKeys: [],
                nonStandard: {},
                scenario: {},
                units: [],
                unit: {},
                scenarioName: "",
                component:"",
                addKey: false,
                newKey: null,
                newValue: null
            }
        },
        methods:{
            addProperty(){
              this.addKey = true;
            },

            saveProperty() {
                this.addKey = false;
                this.scenario[this.newKey] = this.newValue;

                const nonstandard = _.pickBy(this.scenario, (prop, key) => {
                    return !['description', 'longDescription', 'id', 'sName', 'units', 'unitComponent'].includes(key);
                });
                this.nonStandard = nonstandard;
                this.nonStandardKeys = Object.keys(nonstandard);

            },
            newUnit(){
              let unit = {

              }
              if(this.scenario.nationalities && this.scenario.nationalities.length > 0){
                  let nationality  = this.scenario.nationalities[0]
                  Vue.set(unit, 'nationality', nationality )
              }

                Vue.set(unit, 'id', this.units.length);
              this.units.push(unit);
            },
            cancel(){
                window.location.href = document.referrer;
            },
            callme(e1,e2,e3){
                console.log(e1);
                console.log(e2);
                console.log(e3);
                this.scenario.units[e3].num = e1;
            },
            deleteProperty(key){
                delete this.scenario[key];

                const nonstandard = _.pickBy(this.scenario, (prop, key) => {
                    return !['description', 'longDescription', 'id', 'sName', 'units', 'reinforceTurn', 'reinforce', 'nationality'].includes(key);
                });
                this.nonStandard = nonstandard;
                this.nonStandardKeys = Object.keys(nonstandard);
            },

            publish() {
                this.scenario.units = _.map(this.units, (o) => {
                    if (o.deployed === true) {
                        o.reinforceTurn = undefined;
                    }
                    return o;
                });
                this.storeScenario(1, this.scenario);
            },
            storeScenario(id, data) {

                const headers = new Headers;
                headers.append('Content-Type', 'application/json');
                data.units = _.filter(data.units, (o) => o.num > 0);
                data.units = _.map(data.units, (unit) => {
                    unit.movement = unit.movement - 0;
                    unit.combat = unit.combat - 0;
                    unit.range = unit.range - 0;
                    unit.forceId = unit.forceId - 0;
                    unit.num = unit.num - 0;
                    if(typeof unit.facing !== "undefined"){
                        unit.facing = unit.facing - 0;
                    }
                    return unit;
                });
                data.units = _.orderBy(data.units, ['forceId','hq', 'class', 'bow'], ['asc','desc','asc', 'asc']);
                const jsonData = JSON.stringify(data);
                return this.$http.put('/wargame/custom-scenario/' + this.scenarioName + '/' + this.scenario.sName, jsonData, {headers: headers})
                    .then(response => response.json)
                    .then(
                        (myData) => {
                            window.location.href = document.referrer;
                        },
                        (myData) => {
                        }
                    );
            }
        },

        mounted() {

            this.scenarioName = location.pathname.replace(/^.*\//,'')

            this.$http.get('/wargame/custom-scenario/'+this.scenarioName).then(
                response => {
                    return response.json();
                }

            ).then(data => {
                this.scenario = data;

                const nonstandard = _.pickBy(data, (prop, key) => {
                    return !['description', 'longDescription', 'id', 'sName', 'units', 'unitComponent'].includes(key);
                });


                let nationalities = {}
                console.log(this.scenario.units);
                if(this.scenario.nationalities) {
                    _.forEach(this.scenario.nationalities, (value) => {
                        nationalities[value] = value;
                    });
                }

                this.units = _.map(this.scenario.units, (o, k, i) => {
                    console.log(o.nationality);
                    console.log(o.class);
                    o.id = k;
                    nationalities[o.nationality] = o.nationality;
                    if (o.reinforceTurn === undefined) {
                        o.deployed = true;
                    }else {
                        o.deployed = false;
                    }
                    o.shadow = false;
                    return o;
                });
                this.scenario.nationalities = _.keys(nationalities);
                console.log(this.scenario.nationalities);

                this.nonStandard = nonstandard;
                this.nonStandardKeys = Object.keys(nonstandard);
            });
        }
    }
</script>

<style lang="scss" scoped>
    .red{
        color: red;
    }
    .coolBox{
        color: #333333;
        margin-bottom:200px;
    }
</style>
