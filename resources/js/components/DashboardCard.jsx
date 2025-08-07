import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { TrendingUp, TrendingDown, Minus } from "lucide-react"

export default function DashboardCard({ 
    title, 
    value, 
    description, 
    trend, 
    icon: Icon,
    variant = "default" 
}) {
    const getTrendIcon = () => {
        if (trend > 0) return <TrendingUp className="h-3 w-3" />
        if (trend < 0) return <TrendingDown className="h-3 w-3" />
        return <Minus className="h-3 w-3" />
    }

    const getTrendColor = () => {
        if (trend > 0) return "text-green-600"
        if (trend < 0) return "text-red-600"
        return "text-gray-600"
    }

    const getTrendBadgeVariant = () => {
        if (trend > 0) return "secondary"
        if (trend < 0) return "destructive"
        return "outline"
    }

    return (
        <Card className="hover:shadow-lg transition-all duration-300 cursor-pointer hover:scale-105">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium text-muted-foreground">
                    {title}
                </CardTitle>
                {Icon && (
                    <div className="h-4 w-4 text-muted-foreground">
                        <Icon className="h-4 w-4" />
                    </div>
                )}
            </CardHeader>
            <CardContent>
                <div className="text-2xl font-bold mb-1">{value}</div>
                
                {description && (
                    <CardDescription className="text-xs mb-2">
                        {description}
                    </CardDescription>
                )}
                
                {trend !== undefined && (
                    <div className={`flex items-center space-x-1 text-xs ${getTrendColor()}`}>
                        {getTrendIcon()}
                        <Badge variant={getTrendBadgeVariant()} className="text-xs px-1 py-0">
                            {trend > 0 ? '+' : ''}{trend}%
                        </Badge>
                        <span className="text-muted-foreground">vs mois dernier</span>
                    </div>
                )}
            </CardContent>
        </Card>
    )
}