<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import { mdiAccount, mdiAsterisk } from '@mdi/js'
import LayoutGuest from '@/Layouts/LayoutGuest.vue'
import SectionFullScreen from '@/Components/SectionFullScreen.vue'
import CardBox from '@/Components/CardBox.vue'
import FormCheckRadioGroup from '@/Components/FormCheckRadioGroup.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import FormValidationErrors from '@/Components/FormValidationErrors.vue'
import NotificationBarInCard from '@/Components/NotificationBarInCard.vue'
import BaseLevel from '@/Components/BaseLevel.vue'
import { MqResponsive } from "vue3-mq";

const props = defineProps({
  canResetPassword: Boolean,
  status: {
    type: String,
    default: null
  }
})

const form = useForm({
  nip: '',
  password: '',
  remember: []
})

const submit = () => {
  form
    .transform(data => ({
      ... data,
      remember: form.remember && form.remember.length ? 'on' : ''
    }))
    .post(route('login'), {
      onFinish: () => form.reset('password'),
    })
}
</script>

<template>
  <LayoutGuest>
    <Head title="Login" />

    <SectionFullScreen
      v-slot="{ cardClass }"
      bg="dark"
    >
      <CardBox
        :class="cardClass"
        form
        @submit.prevent="submit"
      >
      <MqResponsive target="md-">
        <div class="flex w-ful justify-center items-center mb-4">
          <img src="https://pangansari.com/blog/wp-content/uploads/2023/01/pagansarilogo.gif" alt="" class="h-12">
        </div>
      </MqResponsive>
      
      <div class="flex w-ful justify-center items-center mb-4">
        <p class="text-lg font-bold text-slate-950 dark:text-slate-50">GA-SHEET APP</p>
      </div>
        <FormValidationErrors />

        <NotificationBarInCard 
          v-if="status"
          color="info"
        >
          {{ status }}
        </NotificationBarInCard>

        <FormField
          label="NIP"
          label-for="nip"
          help="Please enter your nip"
        >
          <FormControl
            v-model="form.nip"
            :icon="mdiAccount"
            id="nip"
            autocomplete="nip"
            type="nip"
            required
          />
        </FormField>

        <FormField
          label="Password"
          label-for="password"
          help="Please enter your password"
        >
          <FormControl
            v-model="form.password"
            :icon="mdiAsterisk"
            type="password"
            id="password"
            autocomplete="current-password"
            required
          />
        </FormField>

        <FormCheckRadioGroup
          v-model="form.remember"
          name="remember"
          :options="{ remember: 'Remember' }"
        />

        <BaseDivider />

        <BaseLevel>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Login"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            />
            <!-- <BaseButton
              v-if="canResetPassword"
              :route-name="route('password.request')"
              color="info"
              outline
              label="Remind"
            /> -->
          </BaseButtons>
          <!-- <Link
            :href="route('register')"
          >
            Register
          </Link> -->
        </BaseLevel>
      </CardBox>
    </SectionFullScreen>
  </LayoutGuest>
</template>
