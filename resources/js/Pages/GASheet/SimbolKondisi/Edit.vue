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
  mdiAccountArrowUp,
  mdiSquareEditOutline
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
import useNumberFormat from '../../../Helpers/numberFormat'

//import axios
import axios from 'axios';

const props = defineProps({
  app_url: {
      type: String,
      default: () => "",
  },
  simbol_kondisi: {
      type: Object,
      default: () => ({}),
  },
  master_types: {
      type: Array,
      default: () => [],
  },
  master_type: {
      type: Object,
      default: () => ({}),
  },
});

const { numberFormat } = useNumberFormat();

onMounted(() => {

})

const format = (date) => {
    const day = date.getDate();
    const month = moment(date).format("MMM");
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
}

const swal = inject('$swal');
const disabled = ref(false);
const disabled_form = ref(false);

const form = reactive({
  code: props.simbol_kondisi.code,
  name: props.simbol_kondisi.name,
  is_active: (props.simbol_kondisi.is_active == 1) ? true : false,
  is_active: props.simbol_kondisi.is_active == 1 ? true : false,
  value: props.master_type ? props.master_type : "", 
});

const delay = async (value) => {

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

    if (form.value == "" || form.value == null) {
      swal({
          title: "Peringatan!",
          text: "Type wajib dipilih !",
          icon: "warning",
          showConfirmButton: false,
          timer: 2000,
      });

      return;
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
    console.log(form.value)

  //send data to server
  axios.post(`/ga_sheet/simbol_kondisi/${props.simbol_kondisi.id}`, {
      _method: 'PUT',
      code: form.code,
      name: form.name,
      type: form.value,
      is_active: form.is_active,
  }).then(response => {
    if (response.data.status == 'success') {
      swal({
        title: 'Berhasil!',
        text: response.data.message,
        icon: 'success',
        showConfirmButton: false,
        timer: 2000
      }).then(() => {
        router.visit('/ga_sheet/simbol_kondisi');
      });
    }
  }).catch(error => {
    if (error.response && error.response.data.status == 'error') {
      swal({
        title: 'Gagal!',
        text: error.response.data.message,
        icon: 'error',
        showConfirmButton: false,
        timer: 2000
      });
    }
    disabled.value = false;
  });
}

</script>

<template>
    <LayoutAuthenticated>
        <Head title="Simbol Kondisi" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiSquareEditOutline"
                title="Edit Simbol Kondisi"
                main
            >
                <BaseButton
                    :route-name="route('simbol_kondisi.index')"
                    :icon="mdiArrowLeftBoldOutline"
                    label="Kembali"
                    color="white"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>
            <CardBox>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Kode" :required="true">
                            <FormControl
                                v-model="form.code"
                                type="text"
                                placeholder="Input kode..."
                            >
                            </FormControl>
                        </FormField>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Nama" :required="true">
                            <FormControl
                                v-model="form.name"
                                type="text"
                                placeholder="Input kode..."
                            >
                            </FormControl>
                        </FormField>
                    </div>
                </div>
                <!-- <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Nama" :required="true">
                            <input
                                type="text"
                                placeholder="Input nama..."
                                class="dy-input dy-input-bordered dy-input-info w-full"
                                v-model="form.name"
                            />
                        </FormField>
                    </div>
                </div> -->
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Category" :required="true">
                            <label class="typo__label"></label>
                            <multiselect
                                v-model="form.value"
                                :options="master_types"
                                placeholder="Pilih salah satu"
                                label="name"
                                track-by="id"
                            ></multiselect>
                            <pre class="language-json"></pre>
                        </FormField>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Status" :required="true">
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
                    <BaseButtons class="flex justify-end">
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
