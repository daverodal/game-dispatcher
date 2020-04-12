<template>
  <div id="app">
    <router-view/>
  </div>
</template>
<script>
  export default {
    name: 'area-map-editor',
    props: ['user'],
    created(){
      window.axios.interceptors.request.use(
              (config) => {
                if(config.method === 'get'){
                  config.url = config.url+'?api_token='+this.user.api_token
                }else{
                  config.data = {
                    ...config.data,
                    api_token: this.user.api_token
                  }
                }

                return config;
              },
              (error) => {
                return Promise.regect(error);
              }
      )
    }
  }
</script>
<style lang="scss">
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;

  a {
    font-weight: bold;
    color: #2c3e50;

    &.router-link-exact-active {
      color: #42b983;
    }
  }
}
</style>
