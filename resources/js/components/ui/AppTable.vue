<script setup>
defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  rowKey: { type: String, default: 'Id' },
  emptyTitle: { type: String, default: 'Sin registros' },
  emptyDescription: { type: String, default: 'No se encontraron datos para mostrar.' },
  hoverable: { type: Boolean, default: true },
});
</script>

<template>
  <div class="app-table">
    <table>
      <thead>
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            :style="col.width ? { width: col.width } : undefined"
            :class="col.align && `app-table__th--${col.align}`"
          >
            {{ col.label }}
          </th>
        </tr>
      </thead>
      <tbody v-if="rows.length > 0">
        <tr v-for="(row, rowIndex) in rows" :key="row[rowKey] || row.id" :class="hoverable && 'app-table__row--hoverable'">
          <td
            v-for="col in columns"
            :key="col.key"
            :class="col.align && `app-table__td--${col.align}`"
          >
            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]" :index="rowIndex">
              {{ row[col.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="app-table__loading">
      <div class="app-table__skeleton" v-for="i in 4" :key="i"></div>
    </div>

    <div v-else-if="rows.length === 0" class="app-table__empty">
      <slot name="empty">
        <h4>{{ emptyTitle }}</h4>
        <p>{{ emptyDescription }}</p>
      </slot>
    </div>
  </div>
</template>

<style scoped>
.app-table {
  background: linear-gradient(180deg, rgba(28, 39, 66, 0.55) 0%, rgba(19, 28, 48, 0.65) 100%);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

thead {
  background: rgba(28, 39, 66, 0.4);
}

th {
  padding: 14px 20px;
  font-size: 0.74rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 700;
  color: var(--color-text-muted);
  border-bottom: 1px solid var(--color-border-subtle);
  white-space: nowrap;
}

td {
  padding: 16px 20px;
  font-size: 0.9rem;
  color: var(--color-text-primary);
  border-bottom: 1px solid var(--color-border-subtle);
  vertical-align: middle;
}

tbody tr:last-child td {
  border-bottom: 0;
}

.app-table__row--hoverable {
  transition: background var(--duration-fast) var(--ease-out);
}

.app-table__row--hoverable:hover td {
  background: rgba(99, 102, 241, 0.06);
}

.app-table__th--center,
.app-table__td--center {
  text-align: center;
}
.app-table__th--right,
.app-table__td--right {
  text-align: right;
}

.app-table__loading {
  padding: 18px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.app-table__skeleton {
  height: 36px;
  background: linear-gradient(90deg, var(--color-surface-2) 0%, var(--color-surface-3) 50%, var(--color-surface-2) 100%);
  background-size: 1000px 100%;
  animation: shimmer 1.6s linear infinite;
  border-radius: var(--radius-sm);
}

.app-table__empty {
  padding: 56px 20px;
  text-align: center;
  color: var(--color-text-muted);
}

.app-table__empty h4 {
  margin: 0 0 6px;
  color: var(--color-text-primary);
  font-weight: 700;
}

.app-table__empty p {
  margin: 0;
  font-size: 0.9rem;
}

@keyframes shimmer {
  0% { background-position: -1000px 0; }
  100% { background-position: 1000px 0; }
}
</style>
