<template>
  <div class="py-10">
    <v-row justify="center">
        <v-col cols="10">
            <data-table></data-table>
        </v-col>
    </v-row>
  </div>
</template>

<style>
.flex {
  display: flex;
  flex-direction: row;
}

.flex div {
  margin-left: 10px;
  margin-right: 10px;
}
</style>

<script lang="ts">
import { defineComponent } from "vue";
import DataTable from "./src/components/DataTable.vue";

export default defineComponent({
  components: { DataTable },
});
</script>
