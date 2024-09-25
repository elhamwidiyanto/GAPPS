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
  mdiAccountPlus
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
  name: '',
  nip: '',
  email: '',
  phone: '+62 ',
  com_name: '',
  dept_name: '',
  is_active: false,
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
            text: 'Nama wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }
    if(form.nip == '' || form.nip == null){
        swal({
            title: 'Peringatan!',
            text: 'NIP wajib diisi !',
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

    if(form.phone == '' || form.phone == null){
        swal({
            title: 'Peringatan!',
            text: 'No HP wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }
    if(form.dept_name == '' || form.dept_name == null){
        swal({
            title: 'Peringatan!',
            text: 'No HP wajib diisi !',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000
        });

        return;
    }
    if(form.com_name == '' || form.com_name == null){
        swal({
            title: 'Peringatan!',
            text: 'No HP wajib diisi !',
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
//MASIH ERROR
  //Mengubah +62 menjadi 0 di database
  const convertPhone = (phone) => {
    phone = String(phone);
    if(phone.startsWith('+62 ')){
      return '0' + phone.substring(4);
    }
    return phone;
  }

//method "submit"
const submit = (
  value
) => {
  //Memanggil fungsi convertPhone
  const convertedPhone = convertPhone(form.phone);
  console.log(form.nip);
  
  //send data to server
  router.post(`/ga_sheet/pic`, {
      name: form.name,
      nip: form.nip,
      email: form.email,
      phone: convertedPhone,
      com_name: form.com_name,
      dept_name: form.dept_name,
      is_active: form.is_active,
  }, {
      onSuccess: () => {
          //show success alert
          swal({
              title: 'Berhasil!',
              text: 'Data PIC berhasil ditambahkan!',
              icon: 'success',
              showConfirmButton: false,
              timer: 2000
          });
          form.phone = '+62 ';
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
        :icon="mdiAccountPlus"
        title="Tambah PIC"
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
                    <FormControl
                        v-model="form.name" 
                        type="text"
                        placeholder="Input Nama..."
                    >
                    </FormControl>
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="NIP"
                    :required="true"
                > 
                    <FormControl
                      v-model="form.nip" 
                      type="text"
                      placeholder="Input NIP..."
                    >
                    </FormControl>
                </FormField>
            </div>
        </div>
        <div class="-mx-3 md:flex mb-6">
          <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="Email"
                    :required="true"
                > 
                    <FormControl
                      v-model="form.email"
                      type="text"
                      placeholder="Input Email..." 
                    >
                    </FormControl>
                </FormField>
            </div>
        </div>
        <!-- <div class="-mx-3 md:flex mb-6">
            <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                <FormField
                    label="NO HP"
                    :required="true"
                > 
                <input
                type="text"
                placeholder="Input Nohp..."
                class="dy-input dy-input-bordered dy-input-info w-full"
                v-model="form.phone" />
                </FormField>
            </div>
        </div> -->
    <div class="-mx-3 md:flex mb-6">
        <div class="md:w-1/4 px-3 mb-6 md:mb-0">
        <FormField label="NO HP" :required="true">
            <FormControl
                v-model="form.phone"
                @input="formatPhoneNumber"
                type="text"
                placeholder="Input Nohp..."
            >
            </FormControl>
        </FormField>
        </div>
    </div>
            <div class="-mx-3 md:flex mb-6">
                <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField
                        label="Nama Perusahaan"
                        :required="true"
                    > 
                        <FormControl
                            v-model="form.com_name" 
                            type="text"
                            placeholder="Input Nama Perusahaan..."
                        >
                        </FormControl>
                    </FormField>
                </div>
            </div>
            <div class="-mx-3 md:flex mb-6">
                <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField
                        label="Nama Departemen"
                        :required="true"
                    > 
                        <FormControl
                            v-model="form.dept_name" 
                            type="text"
                            placeholder="Input Nama Departemen..."
                        >
                        </FormControl>
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