<script setup>
import { inject,onMounted,ref,reactive,computed } from 'vue'
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccount,
  mdiAccountCircle,
  mdiLock,
  mdiMail,
  mdiAsterisk,
  mdiFormTextboxPassword,
  mdiArrowLeftBoldOutline,
  mdiAlertBoxOutline,
} from "@mdi/js"
import SectionMain from "@/Components/SectionMain.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseDivider from "@/Components/BaseDivider.vue"
import FormField from "@/Components/FormField.vue"
import FormControl from "@/Components/FormControl.vue"
import BaseButton from "@/Components/BaseButton.vue"
import BaseButtons from "@/Components/BaseButtons.vue"
import NotificationBar from "@/Components/NotificationBar.vue"
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
//import axios
import axios from 'axios';

const props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
});

const swal = inject('$swal');

const steps = [
  {
    target: '.step-6',
    content: 'Gambar TTD!',
    placement: 'bottom',
  },
  {
    target: '.step-7',
    content: 'Hapus TTD!',
    placement: 'bottom',
  },
  {
    target: '.step-8',
    content: 'Simpan TTD!',
    placement: 'bottom',
  },  
  // {
  //   target: '.step-1',
  //   content: 'Nama Pengguna',
  //   placement: 'bottom',
  // },
  // {
  //   target: '.step-2',
  //   content: 'Email Pengguna',
  //   placement: 'bottom',
  // },
  // {
  //   target: '.step-3',
  //   content: 'Untuk merubah password, harap masukkan password sekarang!',
  //   placement: 'bottom',
  // },
  // {
  //   target: '.step-4',
  //   content: 'Masukkan password baru!',
  //   placement: 'bottom',
  // },
  // {
  //   target: '.step-5',
  //   content: 'Masukkan kembali password baru!',
  //   placement: 'bottom',
  // },
  // {
  //   target: '.step-9',
  //   content: 'Simpan perubahan!',
  //   placement: 'bottom',
  // },
];

const signaturePad = ref(null);

// method undo for signature pad
const undo = () => {
    signaturePad.value.undoSignature();
}

const saveSignatureTTD = () => {

  const { isEmpty, data } = signaturePad.value.saveSignature();

  if(isEmpty){
      swal({
          title: 'Peringatan!',
          text: 'TTD wajib di tentukan !',
          icon: 'warning',
          showConfirmButton: false,
          timer: 2000
      });

      return;
  }

  //HTTP request
  axios.post(`/admin/edit-account-info-signature`, {
    signaturePad : data
    })
    .then(response => {

      form.img_signature = response.data.data;

      swal({
          title: 'Berhasil!',
          text: 'TTD berhasil disimpan!',
          icon: 'success',
          showConfirmButton: false,
          timer: 2000
      });

    }).catch(function (error) {
        if (error.response) {
            console.log(error.response)
        }
    });
}

const validPhoneNumber = (event) => {

  const phoneNumberRegex = /^(62)(\d{8,15})$/;

  const isValidPhoneNumber = phoneNumberRegex.test(event);

  if (isValidPhoneNumber) {
      return true;
  } else {
      
    profileForm.phone = '';

      swal({
          title: 'Peringatan!',
          text: 'Format nomor HP belum sesuai, silahkan diawali dengan 62 tidak boleh mengandung spasi harap lihat contoh !',
          icon: 'warning',
          timer: 50000,
      });

      return;
  }
}

const form = reactive({
  img_signature: props.user.path_signature
});

const profileForm = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: '',
})
const passwordForm = useForm({
  old_password: null,
  new_password: null,
  confirm_password: null,
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Profile" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiAccount" title="Profile" main>
        <BaseButton
          :route-name="`/sirkuler/home`"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <NotificationBar
        v-if="$page.props.flash.message"
        color="success"
        :icon="mdiAlertBoxOutline"
      >
        {{ $page.props.flash.message }}
      </NotificationBar>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <CardBox
          title="Edit Profile"
          :icon="mdiAccountCircle"
          form
          @submit.prevent="profileForm.post(route('admin.account.info.store'))"
        >
          <FormField
            label="Name"
            help="Required. Your name"
            :class="{ 'text-red-400': profileForm.errors.name }"
          >
            <FormControl
              v-model="profileForm.name"
              :icon="mdiAccount"
              name="name"
              required
              :error="profileForm.errors.name"
              class="step-1"
            >
              <div class="text-red-400 text-sm" v-if="profileForm.errors.name">
                {{ profileForm.errors.name }}
              </div>
            </FormControl>
          </FormField>
          <FormField
            label="Email"
            help="Required. Your e-mail"
            :class="{ 'text-red-400': profileForm.errors.email }"
          >
            <FormControl
              v-model="profileForm.email"
              :icon="mdiMail"
              type="email"
              name="email"
              required
              :error="profileForm.errors.email"
              class="step-2"
            >
              <div class="text-red-400 text-sm" v-if="profileForm.errors.email">
                {{ profileForm.errors.email }}
              </div>
            </FormControl>
          </FormField>

          <FormField
            label="Phone"
            :required="true"
          >
          <FormControl
            :model-value="user.phone"
            type="number"
            placeholder="0"
            :readonly="true"
          >
          </FormControl>
        </FormField>

        <FormControl
            v-model="profileForm.phone"
            type="number"
            placeholder="Diawali dengan 62, contoh: 628953225353"
            :error="profileForm.errors.phone"
            @blur="validPhoneNumber($event)"
          >
            <div class="text-red-400 text-sm" v-if="profileForm.errors.phone">
              {{ profileForm.errors.phone }}
            </div>
          </FormControl>

          <template #footer>
            <BaseButtons>
              <BaseButton color="info" type="submit" label="Submit" />
            </BaseButtons>
          </template>
        </CardBox>

        <CardBox
          title="Change Password"
          :icon="mdiLock"
          form
          @submit.prevent="
            passwordForm.post(route('admin.account.password.store'), {
              preserveScroll: true,
              onSuccess: () => passwordForm.reset(),
            })
          "
        >
          <FormField
            label="Current password"
            help="Required. Your current password"
            :class="{ 'text-red-400': passwordForm.errors.old_password }"
          >
            <FormControl
              v-model="passwordForm.old_password"
              :icon="mdiAsterisk"
              name="old_password"
              type="password"
              required
              :error="passwordForm.errors.old_password"
              class="step-3"
            >
              <div
                class="text-red-400 text-sm"
                v-if="passwordForm.errors.old_password"
              >
                {{ passwordForm.errors.old_password }}
              </div>
            </FormControl>
          </FormField>

          <BaseDivider />

          <FormField
            label="New password"
            help="Required. New password"
            :class="{ 'text-red-400': passwordForm.errors.new_password }"
          >
            <FormControl
              v-model="passwordForm.new_password"
              :icon="mdiFormTextboxPassword"
              name="new_password"
              type="password"
              required
              :error="passwordForm.errors.new_password"
              class="step-4"
            >
              <div
                class="text-red-400 text-sm"
                v-if="passwordForm.errors.new_password"
              >
                {{ passwordForm.errors.new_password }}
              </div>
            </FormControl>
          </FormField>

          <FormField
            label="Confirm password"
            help="Required. New password one more time"
            :class="{ 'text-red-400': passwordForm.errors.confirm_password }"
          >
            <FormControl
              v-model="passwordForm.confirm_password"
              :icon="mdiFormTextboxPassword"
              name="confirm_password"
              type="password"
              required
              :error="passwordForm.errors.confirm_password"
              class="step-5"
            >
              <div
                class="text-red-400 text-sm"
                v-if="passwordForm.errors.confirm_password"
              >
                {{ passwordForm.errors.confirm_password }}
              </div>
            </FormControl>
          </FormField>

          <BaseButtons>
            <BaseButton type="submit" color="info" label="Submit" class="step-9" />
          </BaseButtons>

          <br>

          <FormField
            label="Existing TTD"
            v-if="form.img_signature"
          >
          
            <div class="bg-white rounded-lg p-6 shadow-lg w-56">
              <!-- Content goes here -->
              <img :src="form.img_signature" alt="">
            </div>
            
          </FormField>

          <FormField
            label="TTD"
            help="Required. New Signature"
          >
          <span class="step-6">
            <VueSignaturePad style="background-color: #D3D3D3; border-radius: 5px;" width="450px" height="150px" ref="signaturePad"/>
          </span>
          </FormField>

          <template #footer>
            <BaseButtons>
              <span class="dy-btn dy-btn-error dy-btn-sm step-7" @click="undo">Hapus TTD</span>
              <span class="dy-btn dy-btn-success dy-btn-sm step-8" @click="saveSignatureTTD">Save TTD</span>
            </BaseButtons>
          </template>

          <VTour :steps="steps" autoStart />
        </CardBox>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>

<!-- <style scoped>

#vjt-tooltip {
  background-color: #d1d1d1;
  color: #fff;
  padding: 0.5rem;
  border-radius: 4px;
  font-size: 13px;
  z-index: 9999;
  max-width: 300px;
}

</style> -->