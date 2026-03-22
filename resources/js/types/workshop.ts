export type Workshop = {
    id: number;
    title: string;
    description: string | null;
    starts_at: string;
    ends_at: string;
    capacity: number;
    available_seats: number;
    is_full: boolean;
    is_registered?: boolean;
    is_on_waiting_list?: boolean;
    registrations_count?: number;
    registrations?: Registration[];
    created_at: string;
    updated_at: string;
};

export type Registration = {
    id: number;
    name: string;
    email: string;
};

export type WorkshopFilters = {
    search?: string;
    start_date?: string;
    end_date?: string;
};

export type WorkshopStats = {
    total: number;
    completed: number;
    upcoming: number;
    total_registrations: number;
};
