import { ref, computed, onMounted, onUnmounted, readonly } from "vue";

export default function useNumberFormat() {
  //method "numberBomItemFormat"
  const numberFormat = (value) => {
    
    let numberToString = String(value);

    return (value != '' && value != undefined && value != null) ? parseFloat(numberToString.replace(',','')).toLocaleString('en-US') : 0;
    
    // value += '';
    // var x = value.split('.');
    // var x1 = x[0];
    // var x2 = x.length > 1 ? '.' + x[1] : '';
    // var rgx = /(\d+)(\d{3})/;
    // while (rgx.test(x1)) {
    //     x1 = x1.replace(rgx, '$1' + ',' + '$2');
    // }

    // return x1 + x2;
  }

  return { numberFormat };
}