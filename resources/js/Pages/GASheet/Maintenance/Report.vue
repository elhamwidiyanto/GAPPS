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
  <div class="container">
    <header class="header">
      <div class="company-logo">
        <img src="/images/company/pangansari.png" alt="Company Logo" />
      </div>
      <div class="document-info">
          <table>
          <tr>
              <td>Doc. No</td>
              <td>: 12345</td>
          </tr>
          <tr>
              <td>Revisi No</td>
              <td>: 1</td>
          </tr>
          <tr>
              <td>Tanggal dibuat</td>
              <td>: 08/08/2024</td>
          </tr>
          </table>
      </div>
    </header>

    <h2 class="title">MONITORING RUANG SERVER HEAD OFFICE</h2>

    <div class="room-info">
      <table>
      <tr>
          <td>Ruang</td>
          <td>: Server</td>
      </tr>
      <tr>
          <td>Bulan/Tahun</td>
          <td>: 08/2024</td>
      </tr>
      </table>
  </div>

    <table class="monitoring-table">
      <thead>
        <tr>
          <th rowspan="2">Tanggal</th>
          <th colspan="31">Tanggal Bulan Ini</th>
        </tr>
        <tr>
          <th v-for="day in 31" :key="day">{{ day }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td v-for="(data) in komponen" :key="data">{{ data }}</td>
          <td v-for="day in 31" :key="'suhu-' + day">{{ checklist[day - 1] }}</td>
        </tr>
      </tbody>
    </table>

    <footer class="footer">
      <div class="notes">
        <p>Catatan:</p>
        <ul>
          <li>1. Berilah Tanda bila normal dan tanda bila tidak, pada kolom hari (1 s/d 31)</li>
          <li>2. Batas tempatur pada suhu normal (15-25)°C</li>
          <li>3. Indikator lampu pada perangkat Router, Switch, UPS dan Backup akan menyala jika perangkat tersebut berjalan normal.</li>
        </ul>
      </div>
      <div class="signatures">
        <div class="created-by">
          <p>Dibuat Oleh</p>
          <p>Nama: </p>
        </div>
        <div class="approved-by">
          <p>Diketahui Oleh</p>
          <p>Nama: </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  data() {
    return {
    };
  }
}
</script>

<style scoped>
body, html {
  margin: 0;
  padding: 0;
  height: 100%;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
}

.container {
  width: 100%;
  min-height: 100vh; 
  max-width: 100vw;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  font-family: Arial, sans-serif;
  padding: 20px;
  box-sizing: border-box;
  color: #000;
  background-color: #fff;
  overflow: auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.company-logo img {
  width: 300px;
}

  .document-info {
  text-align: right; 
  font-family: Arial, sans-serif; 
  font-size: 15px;
  }

  .document-info table {
  width: 100%; 
  border-collapse: collapse; 
  }

  .document-info td {
  padding: 8px; 
  border: 1px solid #000; 
  }

  .document-info tr:nth-child(even) {
  background-color: #ffffff; 
  }

  .document-info td:first-child {
  font-weight: bold; 
  }


  .title {
  text-align: center;
  font-weight: bold;
  margin: 10px 0;
  font-size: 26px;
  border-top: 2px solid #000; 
  border-bottom: 2px solid #000; 
  border-left: 2px solid #000; 
  border-right: 2px solid #000; 
  padding: 10px; 
  box-sizing: border-box; 
  }


.room-info {
font-family: Arial, sans-serif;
display: flex;
justify-content: flex-start; 
font-size: 15px;
margin-bottom: 10px;
}

.room-info table {
width: auto; 
border-collapse: collapse;
}

.room-info td {
padding: 8px;
border: 1px solid #000;
}

.room-info td:first-child {
font-weight: bold;
text-align: left;
}

.room-info tr:nth-child(even) {
background-color: #ffffff;
}



.monitoring-table {
width: 100%;
border-collapse: collapse;
margin-bottom: 20px;
font-size: 12px;
table-layout: auto; 
}

.monitoring-table th,
.monitoring-table td {
border: 1px solid #000;
padding: 8px; 
text-align: center;
white-space: nowrap;
}

.monitoring-table th {
background-color: #f2f2f2;
font-weight: bold;
}

.monitoring-table td {
overflow: hidden;
text-overflow: ellipsis; 
}


.footer {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.notes ul {
  padding-left: 20px;
  list-style: none;
  margin: 0;
}

.signatures {
  display: flex;
  justify-content: space-between;
  width: 50%;
}

.signatures div {
  text-align: center;
  width: 48%;
}

@media print {
  .container {
    padding: 0;
    width: 100%;
    height: 100%;
    max-width: none;
    max-height: none;
  }

  .monitoring-table th, .monitoring-table td {
    padding: 3px;
  }

  .header, .footer, .notes, .signatures {
    margin: 0;
  }
}
</style>
