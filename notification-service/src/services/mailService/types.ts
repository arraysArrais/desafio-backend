type User = {
    name: string;
    email: string;
    balance: string;
};

export type Transaction = {
    id: string;
    value: number;
    sender: User;
    receiver: User;
};