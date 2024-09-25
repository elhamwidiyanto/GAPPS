<script setup>
import { computed } from 'vue'
import { mdiClose } from '@mdi/js'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import CardBox from '@/Components/CardBox.vue'
import OverlayLayer from '@/Components/OverlayLayer.vue'

const props = defineProps({
  title: {
    type: String,
    default: null
  },
  largeTitle: {
    type: String,
    default: null
  },
  button: {
    type: String,
    default: 'info'
  },
  colorButtonConfirm: {
    type: String,
    default: ''
  },
  colorButtonCancel: {
    type: String,
    default: ''
  },
  buttonLabel: {
    type: String,
    default: 'Done'
  },
  buttonConfirmSmall: {
    type: Boolean,
    default: false,
  },
  buttonCancelSmall: {
    type: Boolean,
    default: false,
  },
  hasCancel: Boolean,
  hasConfirm: {
    type: Boolean,
    default: true,
  },
  modelValue: {
    type: [String, Number, Boolean],
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'cancel', 'confirm'])

const value = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})

const confirmCancel = mode => {
  value.value = false
  emit(mode)
}

const confirm = () => confirmCancel('confirm')

const cancel = () => confirmCancel('cancel')
</script>

<template>
  <OverlayLayer
    v-show="value"
    @overlay-click="cancel"
  >
  <!-- w-4/12 md:w-4/12 lg:w-4/12 xl:w-4/12 z-50 -->
    <CardBox
      v-show="value"
      :title="title"
      class="shadow-lg max-h-modal text-gray-950 dark:text-slate-50  md:w-3/5 lg:w-3/5 xl:w-3/5 z-50"
      :header-icon="mdiClose"
      modal
      @header-icon-click="cancel"
    >
      <div class="space-y-1">
        <h1
          v-if="largeTitle"
          class="text-2xl"
        >
          {{ largeTitle }}
        </h1>
        <slot />
      </div>

      <template #footer>
        <BaseButtons>
          <BaseButton
            v-if="hasConfirm"
            :label="buttonLabel"
            :color="(colorButtonConfirm != '') ? colorButtonConfirm : button"
            :small="buttonConfirmSmall"
            @click="confirm"
          />
          <BaseButton
            v-if="hasCancel"
            label="Cancel"
            :color="(colorButtonCancel != '') ? colorButtonCancel : button"
            outline
            :small="buttonCancelSmall"
            @click="cancel"
          />
        </BaseButtons>
      </template>
    </CardBox>
  </OverlayLayer>
</template>
