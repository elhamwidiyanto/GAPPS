<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed, nextTick } from 'vue'
import { router, Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountKey,
  mdiArrowLeftBoldOutline,
  mdiFileDocumentEdit,
  mdiAccountSwitch,
  mdiTrashCanOutline,
  mdiClipboardListOutline,
  mdiStateMachine,
  mdiAccountArrowUp
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
// import FormFilePicker from "@/components/FormFilePicker.vue";
// import BaseDivider from "@/components/BaseDivider.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Multiselect from 'vue-multiselect'
import moment from 'moment'
import useNumberFormat from '../../Helpers/numberFormat'

//import axios
import axios from 'axios';

const props = defineProps({
  app_upload_url: {
    type: String,
    default: () => (''),
  },
  master_type_pihak_lain: {
    type: Array,
    default: () => ([]),
  },
})

const { numberFormat } = useNumberFormat();

onMounted(() => {

})

const format = (date) => {
    const day = date.getDate();
    const month = moment(date).format("MMM");
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
}

const isLoading = ref(false);
const companies = ref([]);

const getCompanies = (value) => {

    isLoading.value = true;

    //HTTP request
    axios.post(`/sirkuler/master_pihak_lain/get_companies`, {
        name: value
    })
    .then(response => {

        companies.value = response.data.data;
        isLoading.value = false;

    }).catch(function (error) {
        
        if (error.response) {
            console.log(error.response)
        }

    });
}

const chooseCompany = (value) => {
    form.code = value.code;
    form.name = value.name;
    form.master_type_pihak_lain = props.master_type_pihak_lain[2];
    disabled_form.value = true;
}

const removeCompany = () => {
    form.code = '';
    form.name = '';
    form.company = '';
    form.master_type_pihak_lain = '';
    disabled_form.value = false;
}

const chooseMasterTypePihakLain = (value) => {
    form.master_type_pihak_lain = value;

    if(value.code == 'INTERNAL'){
        form.code = '';
        form.name = '';
        form.company = '';
    }
}

const swal = inject('$swal');
const disabled = ref(false);
const disabled_form = ref(false);

const form = reactive({
  code: '',
  name: '',
  company: '',
  master_type_pihak_lain: '',
  is_prefix: true,
  is_active: false,
});

const delay = async (value) => {

    if(form.master_type_pihak_lain == '' || form.master_type_pihak_lain == null){
        swal({
            title: 'Peringatan!',
            text: 'Master tipe pihak lain wajib dipilih !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }

    if(form.code == '' || form.code == null){
        swal({
            title: 'Peringatan!',
            text: 'Code wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }

    if(form.name == '' || form.name == null){
        swal({
            title: 'Peringatan!',
            text: 'Nama wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }

    let checkDuplicateCode = await axios.post(`/sirkuler/master_pihak_lain/check_duplicate_code`, {
            
            code: `${form.master_type_pihak_lain.code}-${form.code}`

        }).catch(function (error) {
            
            if (error.response) {
                console.log(error.response)
            }

        });

        if(checkDuplicateCode.data.data == 1){
            swal({
                title: 'Peringatan!',
                text: 'Pihak lain sudah tersedia (duplikasi), input pihak lain yang berbeda !',
                icon: 'warning',
                showConfirmButton: false,
                timer: 5000
            });

            return;
    }

    if(form.company){

        let checkDuplicate = await axios.post(`/sirkuler/master_pihak_lain/check_duplicate`, {
            
            company_id: form.company.id

        }).catch(function (error) {
            
            if (error.response) {
                console.log(error.response)
            }

        });

        if(checkDuplicate.data.data == 1){
            swal({
                title: 'Peringatan!',
                text: 'Silahkan pilih unit / perusahaan lain (duplikasi) !',
                icon: 'warning',
                showConfirmButton: false,
                timer: 2000
            });

            return;
        }
    
    }
    
    disabled.value = true;

    // buttonLoading.value = true;

    // // Re-enable after 5 seconds
    // timeout.value = setTimeout(() => {
    //     disabled.value = false;
    //     buttonLoading.value = false;
    // }, 20000)

    submit(
      value
    );
}

const beforeDestroy = () => {
    // clear the timeout before the component is destroyed
    clearTimeout(timeout)
}

//method "submit"
const submit = (
  value
) => {

  //send data to server
  router.post(`/sirkuler/master_pihak_lain`, {
      code: form.code,
      name: form.name,
      company: form.company,
      submit: value,
      master_type_pihak_lain: form.master_type_pihak_lain,
      is_active: form.is_active,
      is_prefix: form.is_prefix,
  }, {
      onSuccess: () => {
          //show success alert
          swal({
              title: 'Berhasil!',
              text: 'Pihak lain berhasil ditambahkan!',
              icon: 'success',
              showConfirmButton: false,
              timer: 2000
          });
      },
      onError: () => {
        disabled.value = false;
      }
  });
}

</script>

<template>
  <LayoutAuthenticated>
    <Head title="Tambah Pihak Lain" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountArrowUp"
        title="Tambah Pihak Lain"
        main
      >
        <BaseButton
          :route-name="route('master_pihak_lain.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Kembali"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox>
        
        <div class="-mx-3 md:flex mb-6" v-if="disabled_form">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <BaseButton
                    label="Reset"
                    color="danger"
                    @click="removeCompany()"
                />
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Tipe"
                    :required="false"
                >
                    <Multiselect
                        v-model="form.master_type_pihak_lain"
                        :options="master_type_pihak_lain"
                        placeholder="Masukkan tipe pihak lain..."
                        label="name"
                        track-by="id"
                        @select="chooseMasterTypePihakLain($event)"
                        :disabled="disabled_form"
                        :allow-empty="false"
                        >
                        <template v-slot:noResult>
                            Oops! Tipe pihak lain tidak ditemukan.
                        </template>
                    </Multiselect>
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Code"
                    :required="true"
                >
                <div class="-mx-3 md:flex">
                    <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                        <FormControl
                            v-model="form.master_type_pihak_lain.code"
                            type="text"
                            placeholder="Prefix code..."
                            :readonly="true"
                        >
                        </FormControl>
                    </div>
                    <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                        <FormControl
                            v-model="form.code"
                            type="text"
                            placeholder="Input code..."
                            :readonly="disabled_form"
                        >
                        </FormControl>
                    </div>
                </div>
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex" v-if="form.master_type_pihak_lain.code">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Prefix"
                >
                    <FormCheckRadioGroup
                    v-model="form.is_prefix"
                    type="switch"
                    name="notifications-switch"
                    :options="{ outline: '' }"
                    class="mb-2"
                    />
                </FormField>
            </div>
        </div>
        <p class="mb-6"  v-if="form.master_type_pihak_lain.code">*Gunakan prefix jika ingin menambahkan prefix code!</p>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Name"
                    :required="true"
                >
                    <FormControl
                        v-model="form.name"
                        type="text"
                        placeholder="Input nama..."
                        :readonly="disabled_form"
                    >
                    </FormControl>
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Pihak Lain"
                    :required="false"
                >
                    <Multiselect
                        v-model="form.company"
                        :options="companies"
                        @search-change="getCompanies"
                        :loading="isLoading"
                        placeholder="Masukkan nama pihak lain..."
                        label="name"
                        track-by="id"
                        @select="chooseCompany($event)"
                        >
                        <template v-slot:noResult>
                            Oops! Pihak lain tidak ditemukan.
                        </template>
                    </Multiselect>
                </FormField>
            </div>
        </div>
        <p>Jika menambahkan unit internal pangansari, wajib menggunakan pilihan Pihak Lain!</p>
        <br>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Status"
                >
                    <FormCheckRadioGroup
                    v-model="form.is_active"
                    type="switch"
                    name="notifications-switch"
                    :options="{ outline: '' }"
                    class="mb-2"
                    />
                </FormField>
            </div>
        </div>
        

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="success"
              label="Simpan"
              :class="{ 'opacity-25': disabled }"
              :disabled="disabled"
              @click="delay(1)"
            />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>
