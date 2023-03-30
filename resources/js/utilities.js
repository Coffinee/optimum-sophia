import { createToast } from 'mosha-vue-toastify';
import 'mosha-vue-toastify/dist/style.css'


export const errorMessage = function(title,discription,position) {
  new Audio("/sounds/error.mp3").play();
  createToast({title: title,description:discription},{type: "danger",showIcon: true,hideProgressBar: true,position: position,});
}

export const successMessage = function(title,discription,position) {
    new Audio("/sounds/success.mp3").play();
    createToast({
      title: title,
      description: discription
      },
      {
      showIcon: true,
      showCloseButton: false,
      hideProgressBar: true,
      position: position,
      type: 'success',
      transition: 'bounce',
      timeout: 2000,
      })
  }

  export const warningMessage = function(title,discription,position) {
    new Audio("/sounds/warning.mp3").play();
    createToast({
      title: title,
      description: discription
      },
      {
        hideProgressBar: true,
        timeout: 2000,
        type: 'info',
        transition: 'zoom',
        position: position,
        showIcon: true,
      })
  }

  export const eosGetApi =  function(url){
 // const getData = await axios.get('/api/get-inbound-api-token')
    let config = {
          headers: {
          'Authorization': 'Bearer 	3|3HztMssBhdspaHQDLhyW0iGAFmEyMb9ym1VNDrf8'
          }
      }
     return  axios.get('http://127.0.0.1:8001'+url,config);
  }