<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<script setup>
import { computed, reactive, ref, inject, onMounted } from 'vue';
import axios from 'axios';
import moment from 'moment';
import { router, Head } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue';
import SectionMain from '@/Components/SectionMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import CardBox from '@/Components/CardBox.vue';
import { mdiCheckbook } from '@mdi/js';

const props = defineProps({
  user_id: {
    type: Number,
    default: () => 0,
  },
  app_url: {
    type: String,
    default: () => '',
  },
  master_types: {
    type: Array,
    default: () => [],
  },
  errors: {
    type: Object,
    default: () => {},
  }
});

const form = reactive({
  value: null,
  report: null,
  month: null,
  monthString: '',
  alats: [],
  selected_lokasi: [],
  selected_gedung: [],
  selected_ruangan: [],
  master_lokasis: [],
  master_gedungs: [],
  master_ruangans: [],
  errors: '',
  pics: [],
  pic: '',
});

onMounted(() => {
  console.log(props.errors.error);
  
  form.errors = props.errors.error;

  if(form.errors){
    swal({
        title: 'Peringatan!',
        text: `${form.errors}`,
        icon: 'warning',
        showConfirmButton: false,
        timer: 500000
    });

    return;
  }
});

const swal = inject('$swal');

const computedRouteName = computed(() => {
  if (form.value && form.report && form.monthString) {
    return `report/sheet/${form.value.id}/${form.monthString}/${form.report.id}`;
  }
  return '#';
});

const changeFormat = () => {
  if (form.month) {
    form.monthString = moment(form.month).format('YYYY-MM-DD');
    console.log('Updated Month String:', form.monthString);
  } else {
    console.error('form.month is not defined');
  }
};

const stringtifyReport = () => {
  if (form.report && form.report.id) {
    console.log('Report ID:', form.report.id);
  } else {
    console.error('Report ID is not defined');
  }
};

const handleClick = () => {
  console.log()
  changeFormat();
  console.log(form.pic)
  if(form.value.id == 2){
    let url = `report/sheet/${form.value.id}/${form.monthString}/${form.report.id}/${form.pic.pic_name}`;
    window.open(url, '_blank').focus();
  } else if(form.value.id == 3){
    console.log('>>>>>>>.')
    let url = `report/sheet/${form.value.id}/${form.monthString}/${form.report.id}/${form.pic.pic_name}`;
    window.open(url, '_blank').focus();
  }
};

const checkSheet = async (value) => {
  if (value.id === 1) {
    await getLokasi(value);
  } else {
    await get_alat(value);
    form.master_lokasis = [];
    form.master_gedungs = [];
    form.master_ruangans = [];
  }
  console.log(value)
  get_pic(value)
};

const getLokasi = async (value) => {
  try {
    const response = await axios.post('/ga_sheet/report/get_lokasi');
    form.master_lokasis = response.data.lokasis;
    form.master_gedungs = [];
    form.master_ruangans = [];
  } catch (error) {
    console.error(error);
  }
};

const getGedung = async (value) => {
  try {
    const response = await axios.post('/ga_sheet/report/get_gedung', {
      lokasi_id: value.id,
    });
    form.master_gedungs = response.data.gedungs;
    form.master_ruangans = [];
  } catch (error) {
    console.error(error);
  }
};

const getRuangan = async (value) => {
  try {
    const response = await axios.post('/ga_sheet/report/get_ruangan', {
      gedung_id: value.id,
    });
    form.master_ruangans = response.data.ruangans;
    form.alats = [];
  } catch (error) {
    console.error(error);
  }
};

const get_alat = async (value) => {
  form.selected_alat = null;
  if (value) {
    try {
      const response = await axios.post('/ga_sheet/report/get_alat', {
        sheet: value,
      });
      form.alats = response.data.alats;
    } catch (error) {
      console.error(error);
    }
  } else {
    form.selected_alat = null;
    form.alats = [];
  }
};

const get_pic = async (value) => {
  console.log('>>>>>>>>>>>>>',value)
    console.log('>>>>>>>>>><<<<<<<<<<',value)
      const response = await axios.post('/ga_sheet/report/get_pic', {
        sheet: value,
      });
      form.pics = response.data.pics;
      console.log(response)

}

const formatLabel = (option) => {
  if (form.value && form.value.id === 1) {
    return `${option.name}`;
  } else {
    return `${option.name} - ${option.serial_number}`;
  }
};
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Laporan Bulanan" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiCheckbook"
        title="Laporan Bulanan"
        main
      />

      <div>
        <CardBox>
          <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-6">
            <!-- Select Sheet -->
            <div class="dropdown-container">
              <label class="typo__label">Pilih Sheet</label>
              <Multiselect 
                class="mt-2"
                v-model="form.value"
                :options="props.master_types"
                label="name"
                :searchable="true"
                :close-on-select="true"
                :show-labels="true"
                open-direction="bottom"
                placeholder="Pick a value"
                teleport="true"
                @update:modelValue="checkSheet"
              />
            </div>

            <!-- Conditional Fields Based on Sheet Selection -->
            <div v-if="form.value && (form.value.id === 2 || form.value.id === 3)">
              <div class="flex flex-col md:flex-row md:space-x-6 space-y-4 md:space-y-0">
                <!-- Alat Selection -->
                <div class="dropdown-container">
                  <label class="typo__label">Alat</label>
                  <Multiselect 
                    class="mt-2 dropdown"
                    v-model="form.report"
                    :options="form.alats"
                    :custom-label="formatLabel"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    open-direction="bottom"
                    placeholder="Pick a value"
  
                    teleport="true"
                    @update:modelValue="stringtifyReport"
                  />
                </div>
                <div class="dropdown-container">
                  <label class="typo__label">PIC</label>
                  <Multiselect 
                    class="mt-2 dropdown"
                    v-model="form.pic"
                    label="pic_name"
                    :options="form.pics"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    open-direction="bottom"
                    placeholder="Pick a value"
  
                    teleport="true"
                  />
                </div>

                <!-- Month Selection -->
                <div class="dropdown-container">
                  <label class="typo__label">Bulan *</label>
                  <div class="mt-2 dropdown">
                    <VueDatePicker 
                      v-model="form.month"
                      month-picker
                      @update:modelValue="changeFormat"
                      :teleport="true"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Fields for Locations, Buildings, and Rooms -->
            <div v-else>
              <div class="flex flex-col md:flex-row md:space-x-6 space-y-4 md:space-y-0">
                <!-- Location Selection -->
                <div class="dropdown-container">
                  <label class="typo__label">Lokasi</label>
                  <Multiselect 
                    class="mt-2 dropdown"
                    v-model="form.selected_lokasi"
                    :options="form.master_lokasis"
                    :custom-label="formatLabel"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    open-direction="bottom"
                    placeholder="Pick a value"
                    @update:modelValue="getGedung"
  
                    teleport="true"
                  />
                </div>

                <!-- Building Selection -->
                <div class="dropdown-container">
                  <label class="typo__label">Gedung</label>
                  <Multiselect 
                    class="mt-2 dropdown"
                    v-model="form.selected_gedung"
                    :options="form.master_gedungs"
                    :custom-label="formatLabel"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    open-direction="bottom"
                    placeholder="Pick a value"
                    @update:modelValue="getRuangan"
  
                    teleport="true"
                  />
                </div>

                <!-- Room Selection -->
                <div class="dropdown-container">
                  <label class="typo__label">Ruangan</label>
                  <Multiselect 
                    class="mt-2 dropdown"
                    v-model="form.report"
                    :options="form.master_ruangans"
                    :custom-label="formatLabel"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    open-direction="bottom"
                    placeholder="Pick a value"
                    @update:modelValue="changeFormat"
  
                    teleport="true"
                  />
              </div>
              <div class="dropdown-container">
                  <label class="typo__label">PIC</label>
                  <Multiselect 
                    class="mt-2 dropdown"
                    v-model="form.pic"
                    label="pic_name"
                    :options="form.pics"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    open-direction="bottom"
                    placeholder="Pick a value"
  
                    teleport="true"
                    @update:modelValue="get_pic"
                  />
                </div>
            <!-- Month Selection -->
                <div class="dropdown-container">
                  <label class="typo__label">Bulan</label>
                  <div class="mt-2">
                    <VueDatePicker 
                      v-model="form.month"
                      month-picker
                      @update:modelValue="changeFormat"
                      :teleport="true"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Print Button -->
          <BaseButtons class="flex justify-end">
            <BaseButton
              :route-name="computedRouteName"
              type="submit"
              color="info"
              label="Cetak"
              @click="handleClick"
            />
          </BaseButtons>
        </CardBox>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>


<style scoped>
    .dateFilter {
        width: 100%;
        height: 38px;
    }
    .dropdown-container .dropdown {
        width: 200px;
    }
    @media (max-width: 768px) {
        .dropdown-container .dropdown {
            width: 100%;
        }
    }
</style>