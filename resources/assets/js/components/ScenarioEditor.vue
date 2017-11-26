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
            <div v-for="key in nonStandardKeys" class="form-group">
                <label for="keyName">
                    {{ key }}   <button @click="deleteProperty(key)"><i class="fa fa-times red"></i></button>
                    <input type="text" v-model="scenario[key]" class="form-control" id="keyName">
                </label>
            </div>
            <div>
                <p style="font-size:14px;">
                    {{ units }}
                </p>
                <units-list :units="units">

                    <h1>this is the units list From the parent!</h1>
                    <p slot="content" class="small">can you dig that</p>

                </units-list>
            </div>
        </div>
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
                scenarioName: ""
            }
        },
        methods:{

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
                    return !['description', 'longDescription', 'id', 'sName', 'units'].includes(key);
                });
                this.nonStandard = nonstandard;
                this.nonStandardKeys = Object.keys(nonstandard);
            },

            publish() {
                this.scenario.units = _.map(this.scenario.units, (o) => {
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
                    return unit;
                });
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

            this.$http.get('http://localhost:8080/wargame/custom-scenario/'+this.scenarioName).then(
                response => {
                    return response.json();
                }

            ).then(data => {
                this.scenario = data;

                const nonstandard = _.pickBy(data, (prop, key) => {
                    return !['description', 'longDescription', 'id', 'sName', 'units'].includes(key);
                });


                console.log(this.scenario.units);
                this.scenario.units = _.map(this.scenario.units, (o) => {
                    if (o.reinforceTurn === undefined) {
                        o.deployed = true;
                    }else {
                        o.deployed = false;
                    }
                    return o;
                });
                this.units = this.scenario.units;

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
