<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<script setup>
import { onMounted, inject, reactive, ref, computed, nextTick } from 'vue'
import { Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountKey,
  mdiArrowLeftBoldOutline
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import FormCheckRadioGroup from '@/Components/FormCheckRadioGroup.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import Multiselect from 'vue-multiselect'

const props = defineProps({
  formations: {
    type: Array,
    default: () => ([]),
  },
  shifts: {
    type: Array,
    default: () => ([]),
  },
  roles: {
    type: Object,
    default: () => ({}),
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});

const swal = inject('$swal');

const validPhoneNumber = (event) => {

  const phoneNumberRegex = /^(62)(\d{8,15})$/;

  const isValidPhoneNumber = phoneNumberRegex.test(event);

  if (isValidPhoneNumber) {
      return true;
  } else {
      
      form.phone = '';

      swal({
          title: 'Peringatan!',
          text: 'Format nomor HP belum sesuai, silahkan diawali dengan 62 tidak boleh mengandung spasi harap lihat contoh !',
          icon: 'warning',
          timer: 50000,
      });

      return;
  }
}

const form = useForm({
  name: '',
  nip: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  // shift: '',
  // formation: '',
  is_active: false,
  roles: [],
  initial_user: '',
})
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Add user" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountKey"
        title="Add user"
        main
      >
        <BaseButton
          :route-name="route('user.index')"
          :icon="mdiArrowLeftBoldOutline"
          label="Back"
          color="white"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>
      <CardBox
        form
        @submit.prevent="form.post(route('user.store'))"
      >
        <FormField
          label="Name"
          :required="true"
          :class="{ 'text-red-400': errors.name }"
        >
          <FormControl
            v-model="form.name"
            type="text"
            placeholder="Enter Name"
            :error="errors.name"
          >
            <div class="text-red-400 text-sm" v-if="errors.name">
              {{ errors.name }}
            </div>
          </FormControl>
        </FormField>

        <FormField
          label="NIP"
          :required="true"
          :class="{ 'text-red-400': errors.nip }"
        >
          <FormControl
            v-model="form.nip"
            type="text"
            placeholder="Enter NIP"
            :error="errors.nip"
          >
            <div class="text-red-400 text-sm" v-if="errors.nip">
              {{ errors.nip }}
            </div>
          </FormControl>
        </FormField>

        <FormField
          label="Email"
          :required="true"
          :class="{ 'text-red-400': errors.email }"
        >
          <FormControl
            v-model="form.email"
            type="text"
            placeholder="Enter Email"
            :error="errors.email"
          >
            <div class="text-red-400 text-sm" v-if="errors.email">
              {{ errors.email }}
            </div>
          </FormControl>
        </FormField>

        <FormField
          label="Password"
          :required="true"
          :class="{ 'text-red-400': errors.password }"
        >
          <FormControl
            v-model="form.password"
            type="password"
            placeholder="Enter Password"
            :error="errors.password"
          >
            <div class="text-red-400 text-sm" v-if="errors.password">
              {{ errors.password }}
            </div>
          </FormControl>
        </FormField>

        <FormField
          label="Password Confirmation"
          :required="true"
          :class="{ 'text-red-400': errors.password }"
        >
          <FormControl
            v-model="form.password_confirmation"
            type="password"
            placeholder="Enter Password Confirmation"
            :error="errors.password"
          >
            <div class="text-red-400 text-sm" v-if="errors.password">
              {{ errors.password }}
            </div>
          </FormControl>
        </FormField>

        <FormField
          label="Phone"
          :required="true"
          :class="{ 'text-red-400': errors.phone }"
        >
          <FormControl
            v-model="form.phone"
            type="number"
            placeholder="Diawali dengan 62, contoh: 628953225353"
            :error="errors.phone"
            @blur="validPhoneNumber($event)"
          >
            <div class="text-red-400 text-sm" v-if="errors.phone">
              {{ errors.phone }}
            </div>
          </FormControl>
        </FormField>

        <FormField
          label="Initial User"
          :required="true"
          :class="{ 'text-red-400': errors.initial_user }"
        >
          <FormControl
            v-model="form.initial_user"
            type="text"
            placeholder="Input initial user"
            :error="errors.initial_user"
          >
            <div class="text-red-400 text-sm" v-if="errors.initial_user">
              {{ errors.initial_user }}
            </div>
          </FormControl>
          <span>Jika sudah ditentukan tidak dapat diubah!</span>
        </FormField>

        <!-- <div class="-mx-3 md:flex mb-6">
          <div class="md:w-1/2 px-3 mb-6 md:mb-0">
            <FormField
              label="Shift"
              :required="true"
              :class="{ 'text-red-400': errors.shift }"
            >
            <Multiselect
                  v-model="form.shift"
                  :options="shifts"
                  placeholder="Shift..."
                  label="nama"
                  track-by="id"
                  >
                  <template v-slot:noResult>
                      Oops! Shift tidak ditemukan.
                  </template>
            </Multiselect>
            </FormField>
          </div>
          <div class="md:w-1/2 px-3 mb-6 md:mb-0">
            <FormField
              label="Formation"
              :required="true"
              :class="{ 'text-red-400': errors.formation }"
            >
            <Multiselect
                  v-model="form.formation"
                  :options="formations"
                  placeholder="Formation..."
                  label="nama"
                  track-by="id"
                  >
                  <template v-slot:noResult>
                      Oops! Formation tidak ditemukan.
                  </template>
            </Multiselect>
            </FormField>
        </div>
        </div> -->

        <BaseDivider />

        <FormField
          label="Roles"
          :required="true"
          wrap-body
        >
          <FormCheckRadioGroup
            v-model="form.roles"
            name="roles"
            is-column
            :options="props.roles"
          />
        </FormField>

        <BaseDivider />

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

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>
