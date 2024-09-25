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
  mdiAccountEdit,
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
    default: () => (''),
  },
  pic: {
    type: Object,
    default: () => ({}),
  },
})

const { numberFormat } = useNumberFormat();

onMounted(() => {
  if (!form.phone.startsWith('+62')) {
    form.phone = '+62 ';
  }
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
  name: props.pic.pic_name,
  nip: props.pic.pic_nip,
  phone: props.pic.pic_phone,
  email: props.pic.pic_email,
  com_name: props.pic.company_name,
  dept_name: props.pic.departement_name, 
});
const formatPhoneNumber = () => {
  if (form.phone === '' || form.phone === '+62') {
    form.phone = '+62 ';
  } else if (!form.phone.startsWith('+62 ')) {
    form.phone = '+62 ' + form.phone.replace(/^(\+62 |0)/, '');
  } else if (form.phone.startsWith('+62 0')){
    form.phone = form.phone.replace('+62 0', '+62 ');
  }
}
const preventDelete = (event) => {
  const { keyCode, target } = event;
  const { selectionStart } = target;
  
  if (selectionStart <= 4 && (keyCode === 8 || keyCode === 46)) {
    event.preventDefault();
  }
}

const delay = async (value) => {

  if(form.name == '' || form.name == null){
        swal({
            title: 'Peringatan!',
            text: 'Code wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }

    if(form.nip == '' || form.nip == null){
        swal({
            title: 'Peringatan!',
            text: 'Code wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }

    if(form.phone == '' || form.phone == null){
        swal({
            title: 'Peringatan!',
            text: 'Code wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }

    if(form.email == '' || form.email == null){
        swal({
            title: 'Peringatan!',
            text: 'Email wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }
    if(form.com_name == '' || form.com_name == null){
        swal({
            title: 'Peringatan!',
            text: 'Perusahaan wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }
    if(form.dept_name == '' || form.dept_name == null){
        swal({
            title: 'Peringatan!',
            text: 'Departemen wajib diisi !',
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

  //Mengubah +62 menjadi 0 di database
  const convertPhone = (phone) => {
    if(phone.startsWith('+62 ')){
      return '0' + phone.substring(4);
    }
  }

//method "submit"
const submit = (
  value
) => {
    const convertedPhone = convertPhone(form.phone);
  //send data to server
  router.post(`/ga_sheet/pic/${props.pic.id}`, {
      _method: 'PUT',
      pic_name: form.name,
      pic_nip: form.nip,
      pic_phone: convertedPhone,
      pic_email: form.email,
      departement_name: form.dept_name,
      company_name: form.com_name,
  }, {
      onSuccess: () => {
          //show success alert
          swal({
              title: 'Berhasil!',
              text: 'Person in Charge berhasil diubah!',
              icon: 'success',
              showConfirmButton: false,
              timer: 2000
          }).then(() => {
        router.visit('/ga_sheet/pic');
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
    <Head title="Person in Charge" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiSquareEditOutline"
        title="Edit Person in Charge"
        main
      >
        <BaseButton
          :route-name="route('pic.index')"
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
                    label="Nama"
                    :required="true"
                > 
                <input
                type="text"
                placeholder="Input nama..."
                class="dy-input dy-input-bordered dy-input-info w-full"
                v-model="form.name" />
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="NIP"
                    :required="true"
                >
                <input
                type="text"
                placeholder="Input NIP..."
                class="dy-input dy-input-bordered dy-input-info w-full"
                v-model="form.nip" />
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Email"
                    :required="true"
                > 
                <input
                type="text"
                placeholder="Input Email..."
                class="dy-input dy-input-bordered dy-input-info w-full"
                v-model="form.email" />
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="NO HP"
                    :required="true"
                > 
                <input
                type="text"
                placeholder="Input No Hp..."
                class="dy-input dy-input-bordered dy-input-info w-full"
                v-model="form.phone" />
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Perusahaan"
                    :required="true"
                > 
                <input
                type="text"
                placeholder="Input Perusahaan..."
                class="dy-input dy-input-bordered dy-input-info w-full"
                v-model="form.com_name" />
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Departemen"
                    :required="true"
                > 
                <input
                type="text"
                placeholder="Input Departemen..."
                class="dy-input dy-input-bordered dy-input-info w-full"
                v-model="form.dept_name" />
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