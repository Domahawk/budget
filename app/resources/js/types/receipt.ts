import type { ReceiptItem } from '@/types/receiptItem';

export interface Receipt {
    id: number
    image_path: string
    status: string
    ocr_text_raw: string
    ocr_text_edited?: string | null
    items: ReceiptItem[]
}
