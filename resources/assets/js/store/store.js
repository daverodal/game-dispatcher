/**
 * Created by david on 1/5/18.
 */
/**
 * Created by PhpStorm.
 * User: david
 * Date: 1/5/18
 * Time: 3:51 PM

 /*
 * Copyright 2012-2018 David Rodal

 * This program is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
import Vue from 'vue';
import Vuex from 'vuex';

    Vue.use(Vuex);



 export const store = new Vuex.Store({
        state:{
            counter: 1,
            data: {}
        },
     getters:{
            doubleCounter: state =>{
                return state.counter * 3;
            },
         counter: state =>{
             return state.counter;
         }
     }
    })