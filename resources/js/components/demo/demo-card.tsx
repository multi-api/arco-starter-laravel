import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type ReactNode } from 'react';

interface DemoCardProps {
    title: string;
    description: string;
    configured: boolean;
    children: ReactNode;
}

/** Shared shell: title, what the card shows, and a "Not set up" hint when the resource is missing. */
export default function DemoCard({ title, description, configured, children }: DemoCardProps) {
    return (
        <Card>
            <CardHeader>
                <div className="flex items-center justify-between gap-2">
                    <CardTitle>{title}</CardTitle>
                    <Badge variant={configured ? 'default' : 'secondary'}>{configured ? 'On' : 'Off'}</Badge>
                </div>
                <CardDescription>{description}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-3 text-sm">
                {!configured && <p className="text-muted-foreground">Not set up: add it in Arco Cloud → Resources</p>}
                {children}
            </CardContent>
        </Card>
    );
}
