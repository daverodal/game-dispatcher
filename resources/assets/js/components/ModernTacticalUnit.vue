<template>
    <div :class="[unit.class, unit.type, unit.nationality]" class="unit topDiv unit-wrapper smallUnit" :id="unit.id">
        <div class="shadow-mask"></div>
        <span class="unit-desig">{{unit.unitDesig}}</span>

        <div class="counterWrapper">
            <div class="unitNumbers attack">
                {{ unit.attack }}
            </div>
            <div class="unitNumbers range">
                {{unit.range}}
            </div>
            <div class="unitNumbers weapons">
               {{unit.weapons}}
            </div>
            <div class="type-wrapper apc-svg">
                <svg width="16px" height="8px" viewBox="0 0 30 14" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <!-- Generator: Sketch 51.2 (57519) - http://www.bohemiancoding.com/sketch -->
                    <title>Group 3</title>
                    <desc>Created with Sketch.</desc>
                    <defs></defs>
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Group-3" fill-rule="nonzero" stroke="#000000">
                            <g id="Group" transform="translate(1.192661, 6.513761)">
                                <circle id="Oval" fill="#000000" cx="4.53715279" cy="4.44540967" r="1.50962985"></circle>
                                <circle id="Oval-Copy-5" fill="#000000" cx="1.60137297" cy="1.50962985" r="1.50962985"></circle>
                                <circle id="Oval-Copy" fill="#000000" cx="9.21605187" cy="4.44540967" r="1.50962985"></circle>
                                <circle id="Oval-Copy-2" fill="#000000" cx="13.8032078" cy="4.44540967" r="1.50962985"></circle>
                                <circle id="Oval-Copy-3" fill="#000000" cx="18.3903638" cy="4.44540967" r="1.50962985"></circle>
                                <circle id="Oval-Copy-4" fill="#000000" cx="22.9775198" cy="4.44540967" r="1.50962985"></circle>
                                <circle id="Oval-Copy-6" fill="#000000" cx="26.0050427" cy="1.50962985" r="1.50962985"></circle>
                                <path d="M4.51130368,6.30504587 L23.2466047,6.30504587" id="Line" stroke-linecap="square"></path>
                                <path d="M0.0158908405,2.03423946 L3.67078416,5.78447783" id="Line-2" stroke-linecap="square"></path>
                                <path d="M24.0525881,5.71010869 L27.3578829,2.30946882" id="Line-3" stroke-linecap="square"></path>
                            </g>
                            <path d="M23.3089798,0.5 L0.5,0.5 L0.5,8.39908257 L29.0630529,8.39908257 L23.3089798,0.5 Z M29.9135538,8.89908257 L29.5,8.89908257 L29.5,8.83611096 L30,8.83611096 L29.9135538,8.89908257 Z" id="Combined-Shape" fill="#000000"></path>
                        </g>
                    </g>
                </svg>
            </div>
            <div class="type-wrapper artillery-svg">
                <svg width="15" height="21" viewBox="0 0 10 20">
                    <line x1="5" x2="5" y1="0" y2="20" stroke="black" stroke-width="2"></line>
                    <line x1="0" x2="10" y1="10" y2="10"  stroke="black" stroke-width="2"></line>
                    <line x1="1" x2="1" y1="4" y2="16"  stroke="black" stroke-width="2"></line>
                    <line x1="9" x2="9" y1="4" y2="16"  stroke="black" stroke-width="2"></line>
                </svg>
            </div>
            <div class="type-wrapper howitzer-svg">
                <svg width="15" height="21" viewBox="0 0 10 20">
                    <line x1="5" x2="5" y1="0" y2="14" stroke-width="2"></line>
                    <line x1="0" x2="10" y1="9" y2="9"></line>
                    <line x1="1" x2="1" y1="4" y2="14"></line>
                    <line x1="9" x2="9" y1="4" y2="14"></line>
                    <circle r="2" cx="5" cy="17" fill="transparent"></circle>
                </svg>
            </div>
            <div class="type-wrapper anti-tank-svg">
                <svg width="15" height="21" viewBox="0 0 10 20">
                    <line x1="5" x2="5" y1="0" y2="14" stroke-width="2"></line>
                    <line x1="0" x2="10" y1="9" y2="9"></line>
                    <line x1="1" x2="1" y1="4" y2="14"></line>
                    <line x1="9" x2="9" y1="4" y2="14"></line>
                    <line x1="2" x2="5" y1="17" y2="14"></line>
                    <line x1="8" x2="5" y1="17" y2="14"></line>
                </svg>
            </div>
            <div class="type-wrapper mg-svg">
                <svg width="10" height="20" viewBox="0 0 10 20">
                    <line x1="5" x2="5" y1="0" y2="20" stroke-width="2"></line>
                    <line x1="5" x2="0" y1="1" y2="5"></line>
                    <line x1="5" x2="10" y1="1" y2="5"></line>
                    <line x1="0" x2="10" y1="10" y2="10"></line>
                    <line x1="0" x2="10" y1="13" y2="13"></line>
                </svg>
            </div>

            <div class="type-wrapper mech-svg">
                <svg  width='16' height='8'>
                    <g transform="scale(0.08)">
                        <rect x="0" y="0" width="200" height="100" stroke="black" stroke-width="13" fill="none" />
                        <path d="M 70,25 C 20,25 20,75 70,75" stroke="black" stroke-width="8" fill="none" />
                        <path d="M 130,25 C 180,25 180,75 130,75" stroke="black" stroke-width="8" fill="none" />
                        <line x1="0" y1="0" x2="199" y2="99" stroke="black" stroke-width="6" fill="none"/>
                        <line x1="0" y1="99" x2="199" y2="0" stroke="black" stroke-width="6" fill="none"/>
                        <line x1="70" y1="25" x2="130" y2="25" stroke="black" stroke-width="8" fill="none" />
                        <line x1="70" y1="75" x2="130" y2="75" stroke="black" stroke-width="8" fill="none"  />
                    </g>
                </svg>
            </div>
            <div class="type-wrapper armor-svg">
                <svg  width='16' height='8'>
                    <g transform="scale(0.08)">
                        <rect x="0" y="0" width="200" height="100" stroke="black" stroke-width="13" fill="none"></rect>
                        <path d="M 70,25 C 20,25 20,75 70,75" stroke="black" stroke-width="8" fill="none" />
                        <path d="M 130,25 C 180,25 180,75 130,75" stroke="black" stroke-width="8" fill="none" />
                        <line x1="70" y1="25" x2="130" y2="25" stroke="black" stroke-width="8" fill="none" />
                        <line x1="70" y1="75" x2="130" y2="75" stroke="black" stroke-width="8" fill="none"  />
                        <line x1="0" y1="0" x2="200" y2="0" stroke="black" stroke-width="8" fill="none"  />
                        <line x1="0" y1="100" x2="200" y2="100" stroke="black" stroke-width="8" fill="none"  />
                        <line x1="0" y1="0" x2="0" y2="100" stroke="black" stroke-width="8" fill="none"  />
                        <line x1="200" y1="0" x2="200" y2="100" stroke="black" stroke-width="8" fill="none"  />
                    </g>
                </svg>
            </div>
            <div class="type-wrapper infantry-svg">
                <svg width="16" height="8" viewBox="0 0 40 20">
                    <line x1="1" x2="1" y1="0" y2="20" stroke="black" stroke-width="2"></line>
                    <line x1="0" x2="40" y1="19" y2="19" stroke="black" stroke-width="2"></line>
                    <line x1="39" x2="39" y1="0" y2="20" stroke="black"  stroke-width="2"></line>
                    <line x1="0" x2="40" y1="1" y2="1" stroke="black" stroke-width="2"></line>
                    <line x1="1" x2="39" y1="1" y2="19" stroke="black" stroke-width="2"></line>
                    <line x1="1" x2="39" y1="19" y2="1" stroke="black" stroke-width="2"></line>
                </svg>
            </div>
            <div class="type-wrapper cavalry-svg">
                <svg width="12" height="12" viewBox="0 0 20 20">
                    <line x1="1" x2="1" y1="0" y2="20" stroke-width="2"></line>
                    <line x1="0" x2="20" y1="19" y2="19" stroke-width="2"></line>
                    <line x1="19" x2="19" y1="0" y2="20" stroke-width="2"></line>
                    <line x1="0" x2="20" y1="1" y2="1" stroke-width="2"></line>
                    <line x1="1" x2="19" y1="19" y2="1" stroke-width="2"></line>
                </svg>
            </div>
            <div class="type-wrapper truck"><i class="fa fa-truck"></i></div>
            <div class="unitNumbers defense">
                <div v-if="unit.target === 0">
                    [{{unit.defense}}]
                </div>
                <div v-else>
                    {{unit.defense}}
                </div>
            </div>
            <div class="unitNumbers movement">
                {{ unit.movement }}
            </div>
        </div>
    </div>

</template>

<script>

    export default {
        props: ["unit"],
        mounted(){
        }

    }

</script>

<style lang="scss" scoped>
    .left-bracket{
        display:inline-block;
        height:10px;
        width:3px;
        border: 1px solid black;
        border-right: none;
        bottom:3px;
    }
    .right-bracket{
        display:inline-block;
        height:10px;
        width:3px;
        border: 1px solid black;
        border-left: none;
        bottom:3px;
        left:10px;
    }

    .unit {
        background-color: #62bdff;
        &.pushed .shadow-mask {
            background-color: rgba(0, 0, 0, .5) !important;

        }
        .type-wrapper{
            display:none;
            font-size:0px;
            left:8px;

        }
        .counterWrapper {
            height:14px;
        }

        z-index: 3;
        width: 32px;
        height: 32px;
        border: solid 3px black;
        .shadow-mask {
            top: 0px;
            left: 0px;
            height: 100%;
            width: 100%;
            position: absolute;
            background: transparent;
            z-index: 1;
            &.red-shadowy{
                height:50%;
            }
        }
        .unit-numbers {
            text-align: center;
            color: black;
        }
        .unitSize {
            font-size: 9px;
            text-align: center;
            color: black;
        }
        .unit-numbers {
            font-size: 11px;
            line-height:11px;
        }

        &.unit-wrapper {
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
            color: black;
        }


    }

    .unit{
        box-sizing: content-box;
    }
    #map{
    }
    #tecImage{
        width:800px;
    }
    $germanColor: #C7C5C6;
    $britishColor: #f11;
    $angloAlliedColor: $britishColor;
    $angloAlliedWrapperColor: $britishColor;

    $belgianColor: #9f9;
    $frenchColor: #00b9ff;
    $belgianNumberColor: #white;
    $russianColor: #0b0;




    $germanColor: #C7C5C6;
    $britishColor: #9ea837;
    $belgianColor: #ffffe3;
    $frenchColor: #9ea837;
    $belgianNumberColor: #a9b14a;
    $russianColor: #B98700;

    .unit.Soviet{
        background-color:$russianColor;
    }
    .unit.German{
        background-color: $germanColor;
    }
    .German .counterWrapper{
        background-color: $germanColor;
    }
    .American.unit{
        background-color: $belgianNumberColor;
        .counterWrapper{
            background-color: $belgianNumberColor;
        }
    }
    .unit .range{
        color:black;
    }
    .unit-desig{
        top:1px;
        left:1px;
        transform:none;
    }

    .mapSymbols{
        position:absolute;
        color:white;
        background-color: transparent;
        margin-top:-30px;
        margin-left:-35px;
    }
    .col-hex{
        width:65px;
    }

    //.unitNumbers.movement.infoLen3{
    //  font-size:12px;
    //  letter-spacing: -1.8px;
    //}

    .unit .unitNumbers{
        font-size:11px;
        line-height:11px;
    }
    .unitNumbers.defense{
        bottom:1px;
        left:0px;
        letter-spacing: -.6px;
        .defense-number{
            display:inline-block;
            margin-left: 2px;
        }
    }
    .infantry .infantry-svg {

    }

    .infantry {
        .unitNumbers {
            font-size: 11px;
        }
        .infantry-svg {
            display: block;
            position: absolute;
            bottom: 12px;
        }
    }
    .mg {
        .unitNumbers{
            letter-spacing: -1px;
        }
        .mg-svg {
            display: block;
            width: 10px;
            position: absolute;
            bottom: 2px;
            left: 10px;
        }
    }

    .artillery{
        .unitNumbers{
            letter-spacing: -1px;
        }
        .artillery-svg {
            display: block;
            position: absolute;
            bottom: 2px;
            left: 9px;
        }
    }

    .howitzer{
        .unitNumbers{
            letter-spacing: -1px;
        }
        .howitzer-svg {
            display: block;
            position: absolute;
            bottom: 2px;
            left: 9px;
        }
    }

    .anti-tank{
        .unitNumbers{
            letter-spacing: -1px;
        }
        .anti-tank-svg {
            display: block;
            position: absolute;
            bottom: 2px;
            left: 9px;
        }
    }

    .disrupted{
        padding-left:5px;
    }
    #crt{
        width:575px;
        #crtOddsExp{
            text-align:right;
            padding-right:20px;
            float:left;
        }
    }
    .fixed-width{
        width:200px;
    }
    .armor {
        .unitNumbers {
            font-size: 11px;
        }
        .armor-svg {
            display: block;
            position: absolute;
            left: 8px;
            bottom: 12px;
        }
    }

    .truck{
        .unitNumbers {
            font-size:11px;
        }
        .truck{
            font-size:11px;
            position:absolute;
            display:block;
            top:8px;
            left:9px;
        }
    }

    .apc{
        .unitNumbers {
            font-size:11px;
        }
        .apc-svg{
            font-size:11px;
            position:absolute;
            display:block;
            top:8px;
            left:9px;
        }
    }
    .mech {
        .unitNumbers {
            font-size: 11px;
        }
        .mech-svg {
            display: block;
            position: absolute;
            left: 8px;
            bottom: 12px;
        }
    }

    .unitNumbers{
        font-size:13px;
        position:absolute;
        &.attack{
            top:0px;
            left:0px;
        }
        &.range{
            top:0px;
            right:1px;
        }
        &.movement{
            bottom:0px;
            right:1px;
        }
    }


    .unitNumbers.weapons{
        position:static;
        text-align:center;
    }
</style>