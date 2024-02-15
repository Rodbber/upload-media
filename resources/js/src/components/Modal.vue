<template>
  <v-dialog v-model="dialog" width="400px">
    <v-card>
      <div class="modal-body">
        <slot name="modal-body"></slot>
      </div>
      <v-card-actions>
        <v-btn color="primary" block @click="dialog = false">Cancel</v-btn>
      </v-card-actions>
      <v-card-actions>
        <v-btn color="primary" block @click="confirmModal">{{btnText ? btnText:'Confirm'}}</v-btn>
      </v-card-actions>

    </v-card>
  </v-dialog>
</template>

<style scoped>
.modal-body{
    padding: 10px 20px;
}
</style>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: ["isActive", "btnText"],
  emits: ["close", "confirm"],
  data: () => ({
    dialog: false,
  }),
  methods: {
    closeModal() {
      this.$emit("close");
    },
    confirmModal(){
        this.$emit("confirm");
        this.dialog = false
    }
  },
  watch: {
    dialog(n){
        if(!n){
            this.closeModal()
        }
    },
    isActive(n) {
      if (n) {
        this.dialog = n;
      }
    },
  },
});
</script>
