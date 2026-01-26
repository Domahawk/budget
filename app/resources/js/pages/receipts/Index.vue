<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { Store } from '@/types/store';

defineProps<{
    receipts: {
        id: number;
        image_path: string;
        created_at: string;
        store: Store;
        items_sum_total_price: number;
        status: string;
    }[];
}>();

function goToReceipt(id: number) {
    window.location.href = `/receipts/${id}`;
}
</script>

<template>
    <div class="mx-auto min-w-full p-10">
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle>Receipts</CardTitle>
                    <CardDescription>All uploaded receipts</CardDescription>
                </div>

                <a
                    href="/receipts/new"
                    class="rounded bg-black px-4 py-2 text-sm text-white"
                >
                    New receipt
                </a>
            </CardHeader>

            <CardContent>
                <div v-if="receipts.length === 0" class="text-sm text-gray-500">
                    No receipts yet.
                </div>

                <Table v-else>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[70px]">ID</TableHead>
                            <TableHead class="w-[90px]">PREVIEW</TableHead>
                            <TableHead>DATE</TableHead>
                            <TableHead>STORE</TableHead>
                            <TableHead class="text-right">TOTAL</TableHead>
                            <TableHead class="text-right">STATUS</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow
                            v-for="receipt in receipts"
                            :key="receipt.id"
                            class="cursor-pointer transition-colors hover:bg-muted"
                            @click="goToReceipt(receipt.id)"
                        >
                            <TableCell class="font-medium">
                                #{{ receipt.id }}
                            </TableCell>

                            <TableCell>
                                <img
                                    :src="`/storage/${receipt.image_path}`"
                                    class="h-12 w-12 rounded object-cover"
                                    alt=""
                                />
                            </TableCell>

                            <TableCell>
                                {{
                                    new Date(
                                        receipt.created_at,
                                    ).toLocaleString()
                                }}
                            </TableCell>

                            <!-- Placeholder store -->
                            <TableCell class="text-muted-foreground">
                                {{ receipt.store.name }}
                            </TableCell>

                            <!-- Placeholder total -->
                            <TableCell class="text-right font-medium">
                                {{ receipt.items_sum_total_price }}
                            </TableCell>
                            <TableCell class="text-right font-medium">
                                {{ receipt.status }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
