<script setup lang="ts">
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

defineProps<{
    title: string;
    value: string | number;
}>();
</script>

<template>
    <Card
        class="shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md"
    >
        <CardHeader>
            <CardTitle class="text-sm font-medium text-muted-foreground">
                {{ title }}
            </CardTitle>
        </CardHeader>
        <CardContent>
            <p class="text-2xl font-bold tracking-tight">{{ value }}</p>
        </CardContent>
    </Card>
</template>
