<template>
    <div class="relative pb-4">
        <label :for="name"  class="absolute pt-2 text-xs font-bold text-blue-500">{{label}}</label>
        <input type="text" v-model="value" class="text-gray-900 pt-8 w-full focus:outline-none focus:border-blue-400 border-b pb-2" :id="name"
               :placeholder="placeholder" :class="errorClassObject()" @input="updateField">
        <p class="text-red-600" v-text="errorMessage()"></p>
    </div>
</template>

<script>
    export default {
        name: "InputField",
        props: [
            'name',
            'label',
            'placeholder',
            'errors',
            'data'
        ],
        data: () => {
            return {
                value: ''
            }
        },
        computed: {
          hasError(){
              return this.errors && this.errors[this.name] && this.errors[this.name].length > 0
          }
        },

        methods: {
            updateField(){
                this.clearErrors();
                this.$emit('update:field', this.value)
            },
            clearErrors(){
                const field = this.name;
                if(this.hasError){
                    return this.errors[field] = null;
                }
            },
            errorMessage(){
                const field = this.name;
                if(this.hasError){
                    return this.errors[field][0]
                }
            },
            errorClassObject(){
                const field = this.name;
                return { 'error-field': this.hasError }
            }
        },
        watch: {
            data(val){
                this.value = val;
            }
        },
    }
</script>

<style scoped>
.error-field{
    @apply .border-red-500  .border-b-2
}
</style>
