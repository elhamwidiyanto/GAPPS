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
  mdiFileDocumentPlus
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
    default: () => (''),
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

const swal = inject('$swal');
const disabled = ref(false);
const disabled_form = ref(false);

const form = reactive({
  name: '',
  is_active: false,
});

const delay = async (value) => {

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
  router.post(`/ga_sheet/number_of_work`, {
      name: form.name,
      is_active: form.is_active,
  }, {
      onSuccess: () => {
          //show success alert
          swal({
              title: 'Berhasil!',
              text: 'Number Of Work berhasil ditambahkan!',
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
    <Head title="Number Of Work" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiFileDocumentPlus"
        title="Tambah Number Of Work"
        main
      >
        <BaseButton
          :route-name="route('number_of_work.index')"
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
                <FormField
                    label="Name"
                    :required="true"
                > 
                    <FormControl
                        v-model="form.name" 
                        type="text"
                        placeholder="Input nama..."
                    >
                    </FormControl>
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Status"
                    :required="true"
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