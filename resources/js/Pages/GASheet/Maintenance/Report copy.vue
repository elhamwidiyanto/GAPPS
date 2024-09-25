<script setup>
import html2pdf from 'html2pdf.js';
import { inject,onMounted,ref,reactive } from 'vue';

const props = defineProps({
    app_url: {
        type: String,
        default: "",
    },
    result: {
        type: Array,
        default: () => [],
    },
});

const komponen = ref([]);
const checklist = ref([]);

const getKomponen = () => {
  for(let i=0;i<props.result.length;i++){
    const komponenName = props.result[i].komponen_name;
    if (!komponen.value.includes(komponenName)) {
      komponen.value.push(komponenName);
    }
  }
};
const getChecklist = () =>{
  for(let i = 1; i <= getHighestDate(); i++){
    const findDate = props.result.find(record => record.date === i);
    if (findDate && findDate.is_checked === 1) {
      checklist.value.push("V");
    } else {
      checklist.value.push("X");
    }
  }
};

const getHighestDate = () => {
  let highestDate = null;
  for (let i = 0; i < props.result.length; i++) {
    if (highestDate === null || props.result[i].date > highestDate) {
      highestDate = props.result[i].date;
    }
  }
  return highestDate;
};

// exportToPdf = () => {
//       html2pdf(document.getElementById('print-report'), {
//         margin: 1,
//       });
//     }
onMounted(() => {
  getKomponen()
  getChecklist()
  console.log(komponen)
});
</script>
<template>
  <div id="print-report" class="card p-2 bg-white border-black shadow-lg overflow-x-auto">
    <table class="min-w-full bg-white border border-black">
      <thead>
        <tr class="py-1 px-2 border-b border-black bg-white text-black">
          <th class="py-1 px-2 bg-white border-b border-black text-black">Tanggal</th>
          <th v-for="day in 31" :key="day" class="py-1 px-2 bg-white border-b border-black text-black">
            {{ day }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr class="py-1 px-2 border-b border-black bg-white text-black text-center" v-for="(data) in komponen" :key="data">
          <td>{{ data }}</td>
          <td v-for="day in 31" :key="'cek-' + day" class="">
            {{ checklist[day - 1] }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<!-- <script>
export default {
  name: "DataTable",
  methods: {
    exportToPdf() {
      html2pdf(document.getElementById('print-report'), {
        margin: 1,
      });
    }
  }
}; -->

<!-- </script> -->

<style scoped>
.card {
  border: 1px solid black; /* Card border */
  border-radius: 8px; /* Rounded corners */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
  background-color: white; /* Card background color */
  overflow: hidden; /* Ensure content stays within card */
}

table {
  min-width: 100%; /* Ensure table takes full width */
  table-layout: fixed; /* Fix column width */
}

th, td {
  border: 1px solid black;
  width: 32px; /* Set a fixed width for columns */
  padding: 4px; /* Adjust padding for better spacing */
}
</style>