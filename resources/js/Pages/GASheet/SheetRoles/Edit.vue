<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed, nextTick } from "vue";
import { router, Head, Link, useForm } from "@inertiajs/vue3";
import {
    mdiAccountKey,
    mdiArrowLeftBoldOutline,
    mdiFileDocumentEdit,
    mdiAccountSwitch,
    mdiTrashCanOutline,
    mdiClipboardListOutline,
    mdiStateMachine,
    mdiAccountArrowUp,
    mdiSquareEditOutline,
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
// import FormFilePicker from "@/components/FormFilePicker.vue";
// import BaseDivider from "@/components/BaseDivider.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "vue-multiselect";
import moment from "moment";
import useNumberFormat from "../../../Helpers/numberFormat";

//import axios
import axios from "axios";

const props = defineProps({
    app_url: {
        type: String,
        default: () => "",
    },
    sheet_roles: {
        type: Object,
        default: () => ({}),
    },
    lara_users: {
      type: Array,
      default: () => [],
    },
    lara_user: {
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

onMounted(() => {});

const format = (date) => {
    const day = date.getDate();
    const month = moment(date).format("MMM");
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
};

const swal = inject("$swal");
const disabled = ref(false);
const disabled_form = ref(false);

const form = reactive({
    // user_name: props.sheet_roles.user_name,
    is_active: props.sheet_roles.is_active == 1 ? true : false,
    value: props.master_type ? props.master_type : "",
    name: props.lara_user ? props.lara_user : [],
});

const delay = async (value) => {
    // if(form.user_name == '' || form.user_name == null){
    //     swal({
    //         title: 'Peringatan!',
    //         text: 'Proyek wajib diisi !',
    //         icon: 'warning',
    //         showConfirmButton: false,
    //         timer: 2000
    //     });

    //     return;
    // }
    
    
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

    submit(value);
};

const beforeDestroy = () => {
    // clear the timeout before the component is destroyed
    clearTimeout(timeout);
};

//method "submit"
const submit = (value) => {
    //send data to server
    router.post(
        `/ga_sheet/sheet_roles/${props.sheet_roles.id}`,
        {
            _method: "PUT",
            user_id: form.name.id, 
            name: form.name.name,
            is_active: form.is_active,
            type_id: form.value,
        },
        {
            onSuccess: () => {
                //show success alert
                swal({
                    title: "Berhasil!",
                    text: "Sheet Roles berhasil diubah!",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 2000,
                });
            },
            onError: () => {
                disabled.value = false;
            },
        }
    );
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Sheet Roles" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiSquareEditOutline"
                title="Edit Sheet Roles"
                main
            >
                <BaseButton
                    :route-name="route('sheet_roles.index')"
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
                        <div>
                          <FormField label="Pilih User" :required="true">
                            <label class="typo__label px-20"></label>
                            <multiselect
                                v-model="form.name" 
                                :options="lara_users"
                                placeholder="Pilih salah satu"
                                label="name"
                                track-by="id"
                                open-direction="bottom"
                            ></multiselect>
                            <pre class="language-json"></pre>
                          </FormField>
                        </div>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <div>
                            <FormField label="Pilih Tipe" :required="true">
                                <label class="typo__label px-20"></label>
                                <multiselect
                                    v-model="form.value"
                                    :options="master_types"
                                    placeholder="Pilih salah satu"
                                    label="name"
                                    track-by="id"
                                    open-direction="bottom"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                        </div>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Status" :required="true">
                            <FormCheckRadioGroup
                                v-model="form.is_active"
                                type="switch"
                                proyek="notifications-switch"
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
