<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { ReceiptItem } from '@/types/receiptItem';

const props = defineProps<{
    items: ReceiptItem[] | [];
}>();
</script>

<template>
    <div class="w-full">
        <Card class="w-full">
            <CardHeader>
                <CardTitle>Receipt Items</CardTitle>
                <CardDescription>List of items on receipt</CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]"> ID </TableHead>
                            <TableHead>NAME</TableHead>
                            <TableHead>QUANTITY</TableHead>
                            <TableHead class="text-right">PRICE</TableHead>
                            <TableHead class="text-right">TOTAL PRICE</TableHead>
                            <TableHead class="text-right">ACTIONS</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody v-for="item in props.items" :key="item.id">
                        <TableRow>
                            <TableCell class="font-medium"> {{ item.id }} </TableCell>
                            <TableCell>{{ item.item.name }}</TableCell>
                            <TableCell>{{ item.quantity }}</TableCell>
                            <TableCell class="text-right"> {{ item.unit_price }} </TableCell>
                            <TableCell class="text-right"> {{ item.total_price }} </TableCell>
                            <TableCell class="text-right"> Actions </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
